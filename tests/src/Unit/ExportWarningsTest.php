<?php

namespace Drupal\Tests\nodeshop_export\Unit;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\EnforcedResponseException;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\NodeInterface;
use Drupal\nodeshop\Entity\Order;
use Drupal\nodeshop\Entity\OrderProduct;
use Drupal\nodeshop\NodeShop;
use Drupal\nodeshop_export\Export\Export;
use Drupal\nodeshop_export\Export\Helper;
use Drupal\nodeshop_export\Export\Row;
use Drupal\nodeshop_export\Plugin\Action\NodeShopOrderExport;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Psr\Log\LoggerInterface;

/**
 * Tests, that problems of the exported values are reported without changing the CSV.
 *
 * @group nodeshop_export
 */
#[CoversClass(NodeShopOrderExport::class)]
#[CoversClass(Row::class)]
#[CoversClass(Helper::class)]
#[Group('nodeshop_export')]
class ExportWarningsTest extends UnitTestCase
{
    /**
     * The messages shown to the user, keyed by type.
     *
     * @var array
     */
    private $messages = ['status' => [], 'warning' => []];

    /**
     * The logged warnings.
     *
     * @var array
     */
    private $logged = [];

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
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger->method('addMessage')->willReturnCallback(function ($message) use ($messenger) {
            $this->messages['status'][] = (string) $message;
            return $messenger;
        });
        $messenger->method('addWarning')->willReturnCallback(function ($message) use ($messenger) {
            $this->messages['warning'][] = (string) $message;
            return $messenger;
        });
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('warning')->willReturnCallback(function ($message) {
            $this->logged[] = (string) $message;
        });
        $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
        $logger_factory->method('get')->willReturn($logger);

        $container = new ContainerBuilder();
        $container->set('date.formatter', $date_formatter);
        $container->set('messenger', $messenger);
        $container->set('logger.factory', $logger_factory);
        $container->set('string_translation', $this->getStringTranslationStub());
        $container->set('config.factory', $this->getConfigFactoryStub([
            'nodeshop.settings' => ['payment' => ['price_field' => 'field_price'], 'product_features' => []],
        ]));
        // no order history, so the order date is the creation date
        $statement = $this->createMock(StatementInterface::class);
        $statement->method('fetchAll')->willReturn([]);
        $database = $this->createMock(Connection::class);
        $database->method('query')->willReturn($statement);
        $container->set('database', $database);
        \Drupal::setContainer($container);
        NodeShop::$config = null;
    }

    /**
     * Returns the data of a valid row.
     */
    private function getRowData(array $overrides = []): array
    {
        return $overrides + [
            '_order' => 4711,
            'anrede' => 'Frau', 'titel' => '', 'familienname' => 'Muster', 'vorname' => 'Erika', 'land' => 'DE',
            'plz_strasse' => '10117', 'strasse' => 'Musterweg 1', 'ort' => 'Berlin', 'email' => 'erika@example.com',
            'rechnungsart' => 'invoice', 'adressmerkmal1' => '', 'lanrede' => '', 'ltitel' => '',
            'lfamilienname' => '', 'lvorname' => '', 'lland' => 'DE', 'lplz_strasse' => '', 'lstrasse' => ' ',
            'lort' => '', 'lfirma_1' => '', 'bartikelnummer' => 'Art1', 'bmenge' => 1, 'bbestelldatum' => '20261005',
            'bversandkostenjn' => 'n', 'blieferung_ab_ausgabe' => 'Ja',
        ];
    }

    /**
     * Returns the number of columns of a row.
     */
    private function countColumns(string $row): int
    {
        return substr_count($row, ';');
    }

    /**
     * Tests, that a valid row has no warnings and all columns.
     */
    public function testValidRow(): void
    {
        $row = new Row($this->getRowData());

        $this->assertSame(27, $this->countColumns($row->get()));
        $this->assertSame([], $row->getWarnings());
        $this->assertStringNotContainsString('4711', $row->get(), 'The order id is not part of the CSV.');
    }

    /**
     * Tests, that an invalid value is exported as empty column, so the following columns stay in place.
     */
    public function testInvalidValueKeepsColumns(): void
    {
        $row = new Row($this->getRowData(['bmenge' => '2a']));

        $this->assertSame(27, $this->countColumns($row->get()));
        $this->assertStringContainsString(';Art1;;20261005;', $row->get());
        $this->assertSame(
            ['Bestellung 4711: ungültiger Wert "2a" in Spalte "Bestellmenge", die Spalte wurde leer exportiert.'],
            $row->getWarnings()
        );
    }

    /**
     * Tests, that values with a semicolon or a line break are exported unchanged, but reported.
     */
    public function testSemicolonIsReportedButNotChanged(): void
    {
        $row = new Row($this->getRowData(['strasse' => 'Musterweg 1; Hinterhaus', 'ort' => "Ber\nlin"]));

        $this->assertStringContainsString(';Musterweg 1; Hinterhaus;Ber' . "\n" . 'lin;', $row->get());
        $this->assertSame(
            [
                'Bestellung 4711: Spalte "Straße" enthält ein Semikolon oder einen Zeilenumbruch, die Spalten dieser '
                . 'Zeile sind in der CSV verschoben.',
                'Bestellung 4711: Spalte "Ort" enthält ein Semikolon oder einen Zeilenumbruch, die Spalten dieser '
                . 'Zeile sind in der CSV verschoben.',
            ],
            $row->getWarnings()
        );
    }

    /**
     * Tests, that too long values are exported unchanged, but reported.
     */
    public function testTooLongValueIsReportedButNotChanged(): void
    {
        $name = 'Musterfrau-Beispielmann-Testermann';
        $row = new Row($this->getRowData(['familienname' => $name]));

        $this->assertStringContainsString(';' . $name . ';', $row->get());
        $this->assertSame(
            ['Bestellung 4711: Spalte "Familienname" ist länger als 30 Zeichen (34).'],
            $row->getWarnings()
        );
    }

    /**
     * Tests, that the helpers accept empty fields (null) without deprecation warnings.
     */
    public function testHelpersWithEmptyFields(): void
    {
        $this->assertSame('DE', Helper::getCountryCode(null));
        $this->assertSame('', Helper::getSalutation(null));
        $this->assertSame('', Helper::getTitle(null));
        $this->assertSame('', Helper::getPaymentLabel(null));
    }

    /**
     * Creates an order with the given product nodes.
     */
    private function createOrder(int $id, array $nodes): Order
    {
        $order_products = [];
        foreach ($nodes as $node) {
            $order_product = $this->createMock(OrderProduct::class);
            $order_product->method('get')->with('nid')->willReturn((object) ['entity' => $node]);
            $order_product->method('getQuantity')->willReturn(1);
            $order_products[] = $order_product;
        }
        $values = [
            'customer_first_name' => 'Erika', 'customer_last_name' => 'Muster', 'customer_street' => 'Musterweg 1',
            'customer_postal_code' => '10117', 'customer_city' => 'Berlin', 'changed' => 1791190800,
        ];
        $order = $this->createMock(Order::class);
        $order->method('id')->willReturn($id);
        $order->method('getOrderProducts')->willReturn($order_products);
        $order->method('hasField')->willReturnCallback(fn ($field) => 'exported' === $field || isset($values[$field]));
        $order->method('get')->willReturnCallback(fn ($field) => (object) ['value' => $values[$field] ?? null]);
        $order->method('getCustomerEmail')->willReturn('erika@example.com');
        $order->method('getPaymentMethodLabel')->willReturn('Invoice');
        return $order;
    }

    /**
     * Creates an online article.
     */
    private function createStory(): NodeInterface
    {
        $node = $this->createMock(NodeInterface::class);
        $node->method('bundle')->willReturn('story');
        $node->method('getType')->willReturn('story');
        $node->method('hasField')->willReturnCallback(fn ($field) => 'field_price' === $field);
        $node->method('get')->willReturn((object) ['value' => '2.00']);
        return $node;
    }

    /**
     * Tests, that products of deleted nodes are skipped instead of breaking the export.
     */
    public function testProductOfDeletedNodeIsSkipped(): void
    {
        $action = new NodeShopOrderExport([], 'nodeshop_export_export', ['provider' => 'nodeshop_export']);
        $action->execute($this->createOrder(4711, [null, $this->createStory()]));
        $action->execute(null);

        $data = (new \ReflectionProperty(NodeShopOrderExport::class, 'export_data'))->getValue($action);
        $this->assertCount(1, $data);
        $this->assertSame('Art2', $data[0]['bartikelnummer']);
        $this->assertSame(
            ['Bestellung 4711: ein Produkt gehört zu einem gelöschten Inhalt und wurde nicht exportiert.'],
            (new \ReflectionProperty(NodeShopOrderExport::class, 'warnings'))->getValue($action)
        );
    }

    /**
     * Tests, that the download is handed over to Drupal and the orders are marked as exported.
     */
    public function testDownload(): void
    {
        $orders = [];
        $exported = [];
        foreach ([4711, 4712] as $id) {
            $order = $this->createOrder($id, 4711 === $id ? [null, $this->createStory()] : [$this->createStory()]);
            $exported[$id] = (object) ['value' => false];
            $order->method('__get')->with('exported')->willReturn($exported[$id]);
            $order->expects($this->once())->method('addOrderHistoryEntry')->with('Exported order', []);
            $order->expects($this->once())->method('save');
            $orders[] = $order;
        }

        $action = new NodeShopOrderExport([], 'nodeshop_export_export', ['provider' => 'nodeshop_export']);
        try {
            $action->executeMultiple($orders);
            $this->fail('The download should be sent as response.');
        } catch (EnforcedResponseException $exception) {
            $response = $exception->getResponse();
        }

        $this->assertSame('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('attachment; filename=shopbest.csv', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertCount(3, explode("\n", $response->getContent()), 'Header and one row per order.');

        $this->assertTrue($exported[4711]->value);
        $this->assertTrue($exported[4712]->value);
        $this->assertCount(1, $this->messages['status']);
        $warning = 'Bestellung 4711: ein Produkt gehört zu einem gelöschten Inhalt und wurde nicht exportiert.';
        $this->assertSame([$warning], $this->messages['warning']);
        $this->assertSame([$warning], $this->logged);
    }
}
