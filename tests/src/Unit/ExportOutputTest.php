<?php

namespace Drupal\Tests\nodeshop_export\Unit;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\node\NodeInterface;
use Drupal\nodeshop\Entity\Order;
use Drupal\nodeshop\Entity\OrderProduct;
use Drupal\nodeshop\NodeShop;
use Drupal\nodeshop_export\Export\Export;
use Drupal\nodeshop_export\Plugin\Action\NodeShopOrderExport;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests, that the CSV for the Verlags-DB stays exactly the same.
 *
 * The Verlags-DB only imports the file and does not cope with any change of columns, order, charset or content. The
 * expected files in tests/fixtures were generated with the code before any refactoring and must only be changed, if
 * the Verlags-DB requires a new format. Run with UPDATE_EXPORT_FIXTURES=1 to regenerate them.
 *
 * @group nodeshop_export
 */
#[CoversClass(NodeShopOrderExport::class)]
#[CoversClass(Export::class)]
#[Group('nodeshop_export')]
class ExportOutputTest extends UnitTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $date_formatter = $this->createMock(DateFormatterInterface::class);
        $date_formatter->method('format')->willReturnCallback(
            fn ($timestamp, $type = 'medium', $format = '') => gmdate($format, (int) $timestamp)
        );

        $container = new ContainerBuilder();
        $container->set('date.formatter', $date_formatter);
        $container->set('string_translation', $this->getStringTranslationStub());
        $container->set('config.factory', $this->getConfigFactoryStub([
            'nodeshop.settings' => [
                'payment' => ['price_field' => 'field_price', 'default_price' => '1.00'],
                'product_features' => [],
            ],
        ]));
        // no order history, so the order date is the creation date
        $statement = $this->createMock(StatementInterface::class);
        $statement->method('fetchAll')->willReturn([]);
        $database = $this->createMock(Connection::class);
        $database->method('query')->willReturn($statement);
        $container->set('database', $database);
        \Drupal::setContainer($container);

        // NodeShop caches the config statically
        NodeShop::$config = null;
    }

    /**
     * Creates a node with the given bundle and field values.
     */
    private function createNode(string $bundle, array $values): NodeInterface
    {
        $node = $this->createMock(NodeInterface::class);
        $node->method('bundle')->willReturn($bundle);
        $node->method('getType')->willReturn($bundle);
        $node->method('hasField')->willReturnCallback(fn ($field) => array_key_exists($field, $values));
        $node->method('get')->willReturnCallback(fn ($field) => (object) ['value' => $values[$field] ?? null]);
        return $node;
    }

    /**
     * Creates an order with the given values and products as [node, quantity].
     */
    private function createOrder(array $values, string $payment_label, array $products): Order
    {
        $order_products = [];
        foreach ($products as [$node, $quantity]) {
            $order_product = $this->createMock(OrderProduct::class);
            $order_product->method('get')->with('nid')->willReturn((object) ['entity' => $node]);
            $order_product->method('getQuantity')->willReturn($quantity);
            $order_products[] = $order_product;
        }

        // created during the checkout, changed e.g. by a previous export
        $values += ['created' => 1790588400, 'changed' => 1791190800];
        $order = $this->createMock(Order::class);
        $order->method('getOrderProducts')->willReturn($order_products);
        $order->method('hasField')->willReturnCallback(fn ($field) => array_key_exists($field, $values));
        $order->method('get')->willReturnCallback(fn ($field) => (object) ['value' => $values[$field] ?? null]);
        $order->method('getCustomerEmail')->willReturn($values['customer_email'] ?? null);
        $order->method('getPaymentMethodLabel')->willReturn($payment_label);
        return $order;
    }

    /**
     * The customer fields of an order with a delivery address.
     */
    private function getCustomer(array $overrides = []): array
    {
        return $overrides + [
            'customer_title' => 'Frau Dr.',
            'customer_first_name' => 'Erika',
            'customer_last_name' => 'Muster',
            'customer_street' => 'Musterweg 1',
            'customer_street_addon' => 'Hinterhaus',
            'customer_postal_code' => '10117',
            'customer_city' => 'Berlin',
            'customer_country' => 'Deutschland',
            'customer_email' => 'erika@example.com',
            'delivery_title' => 'Herr Prof.',
            'delivery_first_name' => 'Max',
            'delivery_last_name' => 'Mustermann',
            'delivery_street' => 'Ringstraße',
            'delivery_street_number' => '12',
            'delivery_street_addon' => 'Firma GmbH',
            'delivery_postal_code' => '1010',
            'delivery_city' => 'Wien',
            'delivery_country' => 'Österreich',
        ];
    }

    /**
     * Builds the CSV for the given orders like the action does.
     */
    private function export(array $orders): string
    {
        $action = new NodeShopOrderExport([], 'nodeshop_export_export', ['provider' => 'nodeshop_export']);
        foreach ($orders as $order) {
            $action->execute($order);
        }
        $data = (new \ReflectionProperty(NodeShopOrderExport::class, 'export_data'))->getValue($action);
        return implode("\n", (new Export($data))->get());
    }

    /**
     * Compares the CSV with the expected file.
     */
    private function assertMatchesFixture(string $name, string $csv): void
    {
        $file = dirname(__DIR__, 2) . '/fixtures/' . $name . '.csv';
        if (getenv('UPDATE_EXPORT_FIXTURES')) {
            file_put_contents($file, $csv);
        }
        $this->assertFileExists($file);
        $this->assertSame(file_get_contents($file), $csv, 'The CSV for the Verlags-DB changed: ' . $name);
    }

    /**
     * Products of the shop: book, online article, issues with both prices.
     */
    public function testProducts(): void
    {
        $order = $this->createOrder($this->getCustomer(), 'Invoice', [
            [$this->createNode('product', ['field_warenid' => '4711', 'field_price' => '18.00']), 1],
            [$this->createNode('story', ['field_warenid' => '', 'field_price' => '2.00']), 1],
            [$this->createNode('ausgabe', ['field_price' => '9.50']), 2],
            [$this->createNode('ausgabe', ['field_price' => '9.00']), 1],
        ]);

        $this->assertMatchesFixture('products', $this->export([$order]));
    }

    /**
     * Subscriptions (field_warenid 1, 2 or 5) with PayPal and without delivery address.
     */
    public function testSubscriptions(): void
    {
        $customer = array_diff_key(
            $this->getCustomer(['customer_title' => 'Herr', 'customer_country' => 'Schweiz']),
            array_flip(['delivery_title', 'delivery_first_name', 'delivery_last_name', 'delivery_street',
                'delivery_street_number', 'delivery_street_addon', 'delivery_postal_code', 'delivery_city',
                'delivery_country'])
        );
        $orders = [
            $this->createOrder($customer, 'PayPal', [
                [$this->createNode('abo', ['field_warenid' => '1', 'field_preisnr' => '12']), 1],
            ]),
            $this->createOrder($customer, 'PayPal Express', [
                [$this->createNode('abo', ['field_warenid' => '5', 'field_preisnr' => '3']), 1],
            ]),
        ];

        $this->assertMatchesFixture('subscriptions', $this->export($orders));
    }

    /**
     * Orders with empty fields, e.g. without title, country or delivery address.
     */
    public function testEmptyFields(): void
    {
        $order = $this->createOrder(
            [
                'customer_title' => null,
                'customer_first_name' => 'Erika',
                'customer_last_name' => 'Muster',
                'customer_street' => 'Musterweg 1',
                'customer_street_addon' => null,
                'customer_postal_code' => '10117',
                'customer_city' => 'Berlin',
                'customer_country' => null,
                'customer_email' => 'erika@example.com',
                'delivery_title' => null,
                'delivery_first_name' => null,
                'delivery_last_name' => null,
                'delivery_street' => null,
                'delivery_street_number' => null,
                'delivery_street_addon' => null,
                'delivery_postal_code' => null,
                'delivery_city' => null,
                'delivery_country' => null,
            ],
            'Rechnung',
            [[$this->createNode('story', ['field_price' => '1.00']), 1]]
        );

        $this->assertMatchesFixture('empty_fields', $this->export([$order]));
    }
}
