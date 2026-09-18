<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Order\Service\Command\OrderPreparationCommandService;

use App\Core\Ports\{
    Segment\Order\Service\Query\OrderPreparationQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Command\OrderPreparationCommandService
*/
final class OrderPreparationCommandServiceTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private OrderPreparationQueryContract&MockObject $orderPreparationQuery;
    private OrderPreparationCommandService $service;
    private User&MockObject $user;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createMock(User::class);
    }

    public function testPrepareOrderPersistsOrder(): void
    {
        $this->setupPreparationQueryStubs();

        $this->entityPersistence
            ->expects(self::once())
            ->method('persist')
            ->with(self::isInstanceOf(Order::class));

        $this->callPrepareOrder();
    }

    public function testPrepareOrderSetsStatusPending(): void
    {
        $this->setupPreparationQueryStubs();

        self::assertSame(OrderStatus::PENDING, $this->callPrepareOrder()->getStatus());
    }

    public function testPrepareOrderSetsPaymentMethod(): void
    {
        $this->setupPreparationQueryStubs(paymentMethod: OrderPaymentMethod::CASH);

        self::assertSame(OrderPaymentMethod::CASH, $this->callPrepareOrder()->getPayment());
    }

    public function testPrepareOrderSetsSendShippingTrue(): void
    {
        $this->setupPreparationQueryStubs();

        self::assertTrue($this->callPrepareOrder(sendShipping: true)->getSendShipping());
    }

    public function testPrepareOrderSetsSendShippingFalse(): void
    {
        $this->setupPreparationQueryStubs();

        self::assertFalse($this->callPrepareOrder(sendShipping: false)->getSendShipping());
    }

    public function testPrepareOrderSetsInitialPrice(): void
    {
        $this->setupPreparationQueryStubs(totalPrice: 49.99);

        self::assertSame(49.99, $this->callPrepareOrder()->getPrice());
    }

    public function testPrepareOrderGeneratesNonEmptyCode(): void
    {
        $this->setupPreparationQueryStubs();

        self::assertNotEmpty($this->callPrepareOrder()->getCode());
    }

    public function testPrepareOrderAssignsUser(): void
    {
        $this->setupPreparationQueryStubs();

        self::assertSame($this->user, $this->callPrepareOrder()->getUser());
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->orderPreparationQuery = $this->createMock(OrderPreparationQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderPreparationCommandService(
            $this->entityPersistence,
            $this->orderPreparationQuery,
        );
    }

    private function setupPreparationQueryStubs(
        float $totalPrice = 99.99,
        OrderPaymentMethod $paymentMethod = OrderPaymentMethod::CASH,
    ): void {
        $this->orderPreparationQuery->method('validateUserId')->willReturn(1);
        $this->orderPreparationQuery->method('getCartSummary')->willReturn(['total' => $totalPrice]);
        $this->orderPreparationQuery->method('extractTotalPrice')->willReturn($totalPrice);
        $this->orderPreparationQuery->method('resolvePaymentMethod')->willReturn($paymentMethod);
    }

    private function callPrepareOrder(bool $sendShipping = false): Order
    {
        return $this->service->prepareOrder($this->user, $this->buildPayload($sendShipping));
    }

    private function buildPayload(bool $sendShipping = false): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  $sendShipping,
            paymentMethod: OrderPaymentMethod::CASH,
            billing:       new OrderBillingObject(1, 'Main St 1', '12345', 'Bratislava'),
        );
    }
}
