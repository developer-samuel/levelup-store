<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Query;

use Doctrine\Common\Collections\ArrayCollection;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderItem,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus,
    Segment\Product\Entity\Variant\ProductVariant
};

use App\Core\Application\Segment\Order\Service\Query\OrderInvoiceQueryService;

use App\Core\Ports\{
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\Order\Service\Query\OrderInvoiceQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Query\OrderInvoiceQueryService
*/
final class OrderInvoiceQueryServiceTest extends TestCase
{
    private OrderRepositoryContract&MockObject $orderRepository;
    private OrderInvoiceQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderInvoiceQueryContract::class, $this->service);
    }

    public function testGetInvoiceDetailsReturnsArray(): void
    {
        $result = $this->fetchInvoice($this->buildOrder());

        $this->assertArrayHasKey('order', $result);
        $this->assertArrayHasKey('personal', $result);
        $this->assertArrayHasKey('billing', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('hasShipping', $result);
    }

    public function testGetInvoiceDetailsThrowsWhenOrderNotFound(): void
    {
        $this->orderRepository->method('getOrderByCode')->willReturn(null);

        $this->expectException(\RuntimeException::class);

        $this->service->getInvoiceDetails('INVALID');
    }

    public function testGetInvoiceDetailsThrowsWhenPersonalMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->fetchInvoice($this->buildOrder(withPersonal: false));
    }

    public function testGetInvoiceDetailsThrowsWhenBillingMissing(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->fetchInvoice($this->buildOrder(withBilling: false));
    }

    public function testGetInvoiceDetailsThrowsWhenItemsMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->fetchInvoice($this->buildOrder(withItems: false));
    }

    public function testGetInvoiceDetailsThrowsWhenShippingFlagSetButAddressMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->fetchInvoice($this->buildOrder(sendShipping: true, withShipping: false));
    }

    public function testGetInvoiceDetailsContainsCorrectOrderData(): void
    {
        $result = $this->fetchInvoice($this->buildOrder());

        $this->assertIsArray($result['order']);
        $this->assertSame('ORDER-001', $result['order']['code']);
        $this->assertSame(OrderStatus::PROCESSED, $result['order']['status']);
        $this->assertSame(OrderPaymentMethod::CARD, $result['order']['payment']);
        $this->assertSame(99.99, $result['order']['price']);
    }

    public function testGetInvoiceDetailsHasShippingFalseByDefault(): void
    {
        $result = $this->fetchInvoice($this->buildOrder());

        $this->assertFalse($result['hasShipping']);
    }

    private function initMocks(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderInvoiceQueryService($this->orderRepository);
    }

    /**
     * @return array<string, mixed>
    */
    private function fetchInvoice(Order $order, string $code = 'ORDER-001'): array
    {
        $this->orderRepository->method('getOrderByCode')->willReturn($order);

        return $this->service->getInvoiceDetails($code);
    }

    private function buildOrder(
        bool $withPersonal = true,
        bool $withBilling = true,
        bool $withItems = true,
        bool $sendShipping = false,
        bool $withShipping = true,
    ): Order {
        $order = $this->createMock(Order::class);

        $order->method('getCode')->willReturn('ORDER-001');
        $order->method('getStatus')->willReturn(OrderStatus::PROCESSED);
        $order->method('getPayment')->willReturn(OrderPaymentMethod::CARD);
        $order->method('getPrice')->willReturn(99.99);
        $order->method('hasPayment')->willReturn(false);
        $order->method('getSendShipping')->willReturn($sendShipping);

        $order->method('getPersonal')->willReturn(
            $withPersonal ? $this->createMock(OrderPersonal::class) : null,
        );

        $order->method('getBilling')->willReturn(
            $withBilling ? $this->createMock(OrderBilling::class) : null,
        );

        $order->method('getShipping')->willReturn(
            ($sendShipping && $withShipping) ? $this->createMock(OrderShipping::class) : null,
        );

        $items = $withItems
            ? new ArrayCollection([$this->buildOrderItem()])
            : new ArrayCollection();

        $order->method('getItems')->willReturn($items);

        return $order;
    }

    private function buildOrderItem(): OrderItem
    {
        $variant = $this->createMock(ProductVariant::class);
        $variant->method('getId')->willReturn(1);
        $variant->method('getName')->willReturn('Test Variant');

        $item = $this->createMock(OrderItem::class);
        $item->method('getVariant')->willReturn($variant);
        $item->method('getPrice')->willReturn(99.99);

        return $item;
    }
}
