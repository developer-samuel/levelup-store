<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Builder\OrderCommandBuilder,
    Segment\Order\Builder\OrderQueryBuilder,
    Segment\Order\Service\Command\OrderBuildCommandService
};

use App\Core\Ports\{
    Segment\Order\Service\Command\OrderBuildCommandContract,
    Segment\Order\Service\Command\OrderCacheCommandContract,
    Segment\Order\Service\Command\OrderDataCommandContract,
    Segment\Order\Service\Command\OrderItemCommandContract,
    Segment\Order\Service\Command\OrderPreparationCommandContract,
    Segment\Order\Service\Query\OrderCacheQueryContract,
    Segment\Order\Service\Query\OrderCountryQueryContract,
    Segment\Order\Service\Query\OrderItemQueryContract,
    Segment\Order\Service\Query\OrderPaymentQueryContract,
    Segment\Order\Service\Query\OrderPreparationQueryContract,
    Segment\Order\Service\Query\OrderPriceQueryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Command\OrderBuildCommandService
*/
final class OrderBuildCommandServiceTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private OrderPreparationCommandContract&MockObject $orderPreparationCommand;
    private OrderDataCommandContract&MockObject $orderDataCommand;
    private OrderItemQueryContract&MockObject $orderItemQuery;
    private OrderPriceQueryContract&MockObject $orderPriceQuery;
    private OrderCacheCommandContract&MockObject $orderCacheCommand;
    private OrderBuildCommandService $service;
    private User&MockObject $user;
    private OrderCreatePayload $payload;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createMock(User::class);
        $this->payload = $this->buildPayload();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderBuildCommandContract::class, $this->service);
    }

    public function testBuildReturnsOrder(): void
    {
        $order = $this->buildOrderMock();
        $this->orderPreparationCommand->method('prepareOrder')->willReturn($order);

        $result = $this->service->build($this->user, $this->payload, []);

        $this->assertInstanceOf(Order::class, $result);
    }

    public function testBuildCallsPrepareOrderWithUserAndPayload(): void
    {
        $order = $this->buildOrderMock();

        $this->orderPreparationCommand
            ->expects($this->once())
            ->method('prepareOrder')
            ->with($this->user, $this->payload)
            ->willReturn($order);

        $this->service->build($this->user, $this->payload, []);
    }

    public function testBuildAttachesOrderData(): void
    {
        $order = $this->buildOrderMock();

        $this->orderPreparationCommand->method('prepareOrder')->willReturn($order);

        $this->orderDataCommand
            ->expects($this->once())
            ->method('attachOrderData')
            ->with($order, $this->payload);

        $this->service->build($this->user, $this->payload, []);
    }

    public function testBuildSetsCalculatedTotalPriceOnOrder(): void
    {
        $order = $this->buildOrderMock();
        $this->orderPreparationCommand->method('prepareOrder')->willReturn($order);
        $this->orderPriceQuery->method('calculateTotalPrice')->willReturn(149.99);

        $order->expects($this->once())->method('setPrice')->with(149.99);

        $this->service->build($this->user, $this->payload, []);
    }

    public function testBuildFlushesEntityPersistence(): void
    {
        $this->setupPreparedOrder();

        $this->entityPersistence
            ->expects($this->once())
            ->method('flush');

        $this->service->build($this->user, $this->payload, []);
    }

    public function testBuildInvalidatesOrdersCacheForUser(): void
    {
        $this->setupPreparedOrder();

        $this->orderCacheCommand
            ->expects($this->once())
            ->method('invalidateOrdersCache')
            ->with($this->user);

        $this->service->build($this->user, $this->payload, []);
    }

    public function testBuildPassesCartItemsToPrepareLineItems(): void
    {
        $this->setupPreparedOrder();

        $cartItems = [
            $this->createMock(CartItem::class),
            $this->createMock(CartItem::class),
        ];

        $this->orderItemQuery
            ->expects($this->once())
            ->method('prepareLineItems')
            ->with($cartItems)
            ->willReturn([]);

        $this->service->build($this->user, $this->payload, $cartItems);
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->orderPreparationCommand = $this->createMock(OrderPreparationCommandContract::class);
        $this->orderDataCommand = $this->createMock(OrderDataCommandContract::class);
        $this->orderItemQuery = $this->createMock(OrderItemQueryContract::class);
        $this->orderPriceQuery = $this->createMock(OrderPriceQueryContract::class);
        $this->orderCacheCommand = $this->createMock(OrderCacheCommandContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderBuildCommandService(
            $this->entityPersistence,
            $this->buildCommandBuilder(),
            $this->buildQueryBuilder(),
        );
    }

    private function buildCommandBuilder(): OrderCommandBuilder
    {
        return new OrderCommandBuilder(
            orderDataCommand:        $this->orderDataCommand,
            orderPreparationCommand: $this->orderPreparationCommand,
            orderItemCommand:        $this->createMock(OrderItemCommandContract::class),
            orderCacheCommand:       $this->orderCacheCommand,
        );
    }

    private function buildQueryBuilder(): OrderQueryBuilder
    {
        return new OrderQueryBuilder(
            orderCountryQuery:     $this->createMock(OrderCountryQueryContract::class),
            orderPreparationQuery: $this->createMock(OrderPreparationQueryContract::class),
            orderItemQuery:        $this->orderItemQuery,
            orderPriceQuery:       $this->orderPriceQuery,
            orderPaymentQuery:     $this->createMock(OrderPaymentQueryContract::class),
            orderValidatorQuery:   $this->createMock(OrderValidatorQueryContract::class),
            orderCacheQuery:       $this->createMock(OrderCacheQueryContract::class),
        );
    }

    private function buildPayload(): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  false,
            paymentMethod: OrderPaymentMethod::CASH,
            billing:       new OrderBillingObject(1, 'Main St 1', '12345', 'Bratislava'),
        );
    }

    private function buildOrderMock(): Order&MockObject
    {
        $order = $this->createMock(Order::class);
        $order->method('setPrice')->willReturnSelf();

        return $order;
    }

    private function setupPreparedOrder(): void
    {
        $this->orderPreparationCommand->method('prepareOrder')->willReturn($this->buildOrderMock());
    }
}
