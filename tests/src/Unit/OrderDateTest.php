<?php

namespace Drupal\Tests\nodeshop_export\Unit;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\nodeshop\Entity\Order;
use Drupal\nodeshop\Form\OrderCheckoutForm;
use Drupal\nodeshop_export\Plugin\Action\NodeShopOrderExport;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests, that the order date is the date of the checkout and not of the creation or the last change.
 *
 * @group nodeshop_export
 */
#[CoversClass(NodeShopOrderExport::class)]
#[Group('nodeshop_export')]
class OrderDateTest extends UnitTestCase
{
    const CREATED = 1790000000;
    const CHANGED = 1791190800;

    /**
     * The parameters of the last database query.
     *
     * @var array
     */
    private $query_arguments = [];

    /**
     * Returns the order date for an order with the given history.
     *
     * @param array $history The history entries as [entry, variables, timestamp], sorted by time
     * @param int|null $created The creation date of the order
     */
    private function getOrderDate(array $history, ?int $created = self::CREATED): int
    {
        $statement = $this->createMock(StatementInterface::class);
        $statement->method('fetchAll')->willReturn(array_map(
            fn ($entry) => (object) ['entry' => $entry[0], 'variables' => $entry[1], 'timestamp' => (string) $entry[2]],
            $history
        ));
        $database = $this->createMock(Connection::class);
        $database->method('query')->willReturnCallback(function ($query, $arguments) use ($statement) {
            $this->query_arguments = $arguments;
            return $statement;
        });
        $container = new ContainerBuilder();
        $container->set('database', $database);
        \Drupal::setContainer($container);

        $values = ['created' => $created, 'changed' => self::CHANGED];
        $order = $this->createMock(Order::class);
        $order->method('id')->willReturn(4711);
        $order->method('get')->willReturnCallback(fn ($field) => (object) ['value' => $values[$field]]);

        $action = new NodeShopOrderExport([], 'nodeshop_export_export', ['provider' => 'nodeshop_export']);
        $method = new \ReflectionMethod($action, 'getOrderDate');
        return $method->invoke($action, $order);
    }

    /**
     * Returns a history entry for a change of the status.
     */
    private static function statusEntry(string $old, string $new, int $timestamp): array
    {
        return [
            NodeShopOrderExport::HISTORY_STATUS_CHANGED,
            serialize(['@old_status' => $old, '@new_status' => $new]),
            $timestamp,
        ];
    }

    public static function historyProvider(): array
    {
        $finished = [NodeShopOrderExport::HISTORY_CHECKOUT_FINISHED, serialize([]), 1790500000];
        return [
            'completed after the payment' => [
                [$finished, self::statusEntry('in_checkout', 'payment', 1790500010), self::statusEntry('payment', 'completed', 1790600000)],
                1790600000,
            ],
            'first completion counts, e.g. after a reset by an admin' => [
                [
                    $finished,
                    self::statusEntry('in_checkout', 'completed', 1790500010),
                    self::statusEntry('completed', 'payment', 1790700000),
                    self::statusEntry('payment', 'completed', 1790800000),
                ],
                1790500010,
            ],
            'checkout finished, but not completed' => [
                [$finished, self::statusEntry('in_checkout', 'payment', 1790500010)],
                1790500000,
            ],
            'no history, e.g. migrated from the lightshop' => [[], self::CREATED],
            'broken variables' => [
                [[NodeShopOrderExport::HISTORY_STATUS_CHANGED, 'O:8:"stdClass":0:{}', 1790500010]],
                self::CREATED,
            ],
        ];
    }

    /**
     * Tests the order date for different histories.
     */
    #[DataProvider('historyProvider')]
    public function testOrderDate(array $history, int $expected): void
    {
        $this->assertSame($expected, $this->getOrderDate($history));
        $this->assertSame(4711, $this->query_arguments[':order_id']);
    }

    /**
     * Tests, that the last change is only used without creation date.
     */
    public function testLastChangeAsFallback(): void
    {
        $this->assertSame(self::CHANGED, $this->getOrderDate([], null));
    }

    /**
     * Tests, that the NodeShop still writes the history entries, the order date is taken from.
     *
     * Otherwise the export would silently fall back to the creation date of the orders.
     */
    public function testHistoryEntriesOfTheNodeShop(): void
    {
        $checkout_form = file_get_contents((new \ReflectionClass(OrderCheckoutForm::class))->getFileName());
        $this->assertStringContainsString(
            "addOrderHistoryEntry('" . NodeShopOrderExport::HISTORY_CHECKOUT_FINISHED . "')",
            $checkout_form
        );

        $order = file_get_contents((new \ReflectionClass(Order::class))->getFileName());
        $this->assertStringContainsString("'" . NodeShopOrderExport::HISTORY_STATUS_CHANGED . "'", $order);
        $this->assertStringContainsString("'@new_status' => \$status", $order);
        $this->assertSame('completed', Order::NODESHOP_ORDER_STATUS_COMPLETED);
    }
}
