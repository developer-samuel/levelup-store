<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Command;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    Segment\Order\ValueObject\OrderResultObject,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Builder\OrderCommandBuilder,
    Segment\Order\Builder\OrderQueryBuilder,
    Segment\Order\Service\Command\OrderMutationCommandService
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Notifier\OrderConfirmationNotifierContract,
    Segment\Order\Service\Command\OrderBuildCommandContract,
    Segment\Order\Service\Command\OrderCacheCommandContract,
    Segment\Order\Service\Command\OrderDataCommandContract,
    Segment\Order\Service\Command\OrderItemCommandContract,
    Segment\Order\Service\Command\OrderMutationCommandContract,
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
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Command\OrderMutationCommandService
*/
class OrderMutationCommandServiceTest extends TestCase
{
    private SecurityProviderContract&MockObject $securityProvider;
    private OrderBuildCommandContract&MockObject $orderBuildCommand;
    private OrderConfirmationNotifierContract&MockObject $notifier;
    private EntityPersistenceContract&MockObject $entityPersistence;
    private OrderValidatorQueryContract&MockObject $orderValidatorQuery;
    private OrderItemQueryContract&MockObject $orderItemQuery;
    private OrderPaymentQueryContract&MockObject $orderPaymentQuery;
    private OrderItemCommandContract&MockObject $orderItemCommand;
    private OrderMutationCommandService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderMutationCommandContract::class, $this->service);
    }

    public function testCreateOrderReturnsOrderResultForCashPayment(): void
    {
        $this->setupCashOrderCreation();

        $result = $this->service->createOrder($this->buildPayload(OrderPaymentMethod::CASH));

        $this->assertInstanceOf(OrderResultObject::class, $result);
        $this->assertInstanceOf(Order::class, $result->order);
        $this->assertNull($result->paymentUrl);
    }

    public function testCreateOrderReturnsPaymentUrlForCardPayment(): void
    {
        $this->setupCardPaymentInitiation(paymentUrl: 'https://stripe.com/pay/abc');

        $result = $this->service->createOrder($this->buildPayload(OrderPaymentMethod::CARD));

        $this->assertNull($result->order);
        $this->assertSame('https://stripe.com/pay/abc', $result->paymentUrl);
    }

    public function testCreateOrderSendsNotifierForCashPayment(): void
    {
        $this->setupCashOrderCreation();

        $this->notifier
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Order::class));

        $this->service->createOrder($this->buildPayload(OrderPaymentMethod::CASH));
    }

    public function testCreateOrderDoesNotSendNotifierForCardPayment(): void
    {
        $this->setupCardPaymentInitiation();

        $this->notifier
            ->expects($this->never())
            ->method('send');

        $this->service->createOrder($this->buildPayload(OrderPaymentMethod::CARD));
    }

    public function testCreateOrderThrowsWhenUserNotFound(): void
    {
        $this->securityProvider->method('getCurrentUser')->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        $this->service->createOrder($this->buildPayload(OrderPaymentMethod::CASH));
    }

    public function testCreateOrderCallsBuildCommandForCashPayment(): void
    {
        $this->setupUserWithCartItems();

        $user = $this->securityProvider->getCurrentUser();
        $order = $this->createMock(Order::class);
        $order->method('getUser')->willReturn($user);

        $this->orderBuildCommand
            ->expects($this->once())
            ->method('build')
            ->willReturn($order);

        $this->orderItemCommand->method('processOrderItems');
        $this->notifier->method('send');

        $this->entityPersistence
            ->method('wrapInTransaction')
            ->willReturnCallback(fn (callable $cb) => $cb());

        $this->service->createOrder($this->buildPayload(OrderPaymentMethod::CASH));
    }

    private function setupUserWithCartItems(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        $this->securityProvider->method('getCurrentUser')->willReturn($user);

        $cartItem = $this->createMock(CartItem::class);
        $this->orderValidatorQuery->method('getCartItemsOrFail')->willReturn([$cartItem]);
    }

    private function setupCashOrderCreation(): void
    {
        $this->setupUserWithCartItems();

        $user = $this->securityProvider->getCurrentUser();
        $order = $this->createMock(Order::class);
        $order->method('getUser')->willReturn($user);

        $this->orderBuildCommand->method('build')->willReturn($order);
        $this->orderItemCommand->method('processOrderItems');
        $this->notifier->method('send');

        $this->entityPersistence
            ->method('wrapInTransaction')
            ->willReturnCallback(fn (callable $cb) => $cb());
    }

    private function setupCardPaymentInitiation(string $paymentUrl = 'https://stripe.com/pay/test'): void
    {
        $this->setupUserWithCartItems();

        $this->orderItemQuery->method('prepareLineItems')->willReturn([]);
        $this->orderPaymentQuery->method('initiateCardPayment')->willReturn($paymentUrl);
    }

    private function initMocks(): void
    {
        $this->securityProvider = $this->createMock(SecurityProviderContract::class);
        $this->orderBuildCommand = $this->createMock(OrderBuildCommandContract::class);
        $this->notifier = $this->createMock(OrderConfirmationNotifierContract::class);
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->orderValidatorQuery = $this->createMock(OrderValidatorQueryContract::class);
        $this->orderItemQuery = $this->createMock(OrderItemQueryContract::class);
        $this->orderPaymentQuery = $this->createMock(OrderPaymentQueryContract::class);
        $this->orderItemCommand = $this->createMock(OrderItemCommandContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderMutationCommandService(
            entityPersistence:   $this->entityPersistence,
            securityProvider:    $this->securityProvider,
            orderQueryBuilder:   $this->buildQueryBuilder(),
            orderCommandBuilder: $this->buildCommandBuilder(),
            orderBuildCommand:   $this->orderBuildCommand,
            notifier:            $this->notifier,
        );
    }

    private function buildQueryBuilder(): OrderQueryBuilder
    {
        return new OrderQueryBuilder(
            orderCountryQuery:     $this->createMock(OrderCountryQueryContract::class),
            orderPreparationQuery: $this->createMock(OrderPreparationQueryContract::class),
            orderItemQuery:        $this->orderItemQuery,
            orderPriceQuery:       $this->createMock(OrderPriceQueryContract::class),
            orderPaymentQuery:     $this->orderPaymentQuery,
            orderValidatorQuery:   $this->orderValidatorQuery,
            orderCacheQuery:       $this->createMock(OrderCacheQueryContract::class),
        );
    }

    private function buildCommandBuilder(): OrderCommandBuilder
    {
        return new OrderCommandBuilder(
            orderDataCommand:        $this->createMock(OrderDataCommandContract::class),
            orderPreparationCommand: $this->createMock(OrderPreparationCommandContract::class),
            orderItemCommand:        $this->orderItemCommand,
            orderCacheCommand:       $this->createMock(OrderCacheCommandContract::class),
        );
    }

    private function buildPayload(OrderPaymentMethod $paymentMethod): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  false,
            paymentMethod: $paymentMethod,
            billing:       new OrderBillingObject(1, 'Main St 1', '12345', 'Bratislava'),
        );
    }
}
