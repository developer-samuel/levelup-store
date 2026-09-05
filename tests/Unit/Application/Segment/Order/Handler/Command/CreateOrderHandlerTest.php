<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Shared\Exception\ConflictException,
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\Order\ValueObject\OrderResultObject,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Order\Handler\Command\CreateOrderHandler;

use App\Core\Ports\{
    Security\Policy\SecurityPolicyContract,
    Security\Provider\SecurityProviderContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Segment\Order\Handler\Command\CreateOrderHandlerContract,
    Segment\Order\Service\Command\OrderMutationCommandContract,
    Shared\Logging\AppLoggerContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Handler\Command\CreateOrderHandler
*/
class CreateOrderHandlerTest extends TestCase
{
    private SecurityPolicyContract&MockObject $securityPolicy;
    private SecurityProviderContract&MockObject $securityProvider;
    private CartRenderQueryContract&MockObject $cartRenderQuery;
    private OrderMutationCommandContract&MockObject $orderMutationCommand;
    private AppLoggerContract&MockObject $logger;
    private CreateOrderHandler $handler;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CreateOrderHandlerContract::class, $this->handler);
    }

    public function testHandleChecksPolicyBeforeCreatingOrder(): void
    {
        $this->securityPolicy
            ->expects($this->once())
            ->method('checkIfEmailVerified')
            ->willReturn($this->createMock(User::class));

        $this->orderMutationCommand
            ->method('createOrder')
            ->willReturn(new OrderResultObject(order: $this->createMock(Order::class), paymentUrl: null));

        $this->handler->handle($this->buildPayload());
    }

    public function testHandleReturnsSuccessForCashOrder(): void
    {
        $this->setupVerifiedUser();
        $this->setupCashOrderResult(1);

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('success', $result['status']);
        $this->assertSame('success', $result['redirect']);
    }

    public function testHandleReturnsSuccessWithPaymentUrlForCardPayment(): void
    {
        $this->setupVerifiedUser();

        $this->orderMutationCommand
            ->method('createOrder')
            ->willReturn(new OrderResultObject(order: null, paymentUrl: 'https://stripe.com/pay/abc'));

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('success', $result['status']);
        $this->assertSame('https://stripe.com/pay/abc', $result['redirect']);
    }

    public function testHandleReturnsErrorWhenPolicyThrowsAccessDenied(): void
    {
        $this->securityPolicy
            ->method('checkIfEmailVerified')
            ->willThrowException(new AccessDeniedException('Email not verified.'));

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('error', $result['status']);
        $this->assertSame(403, $result['code']);
        $this->assertSame('Email not verified.', $result['message']);
    }

    public function testHandleReturnsErrorWhenMutationCommandThrows(): void
    {
        $this->setupCreateOrderThrows(new \DomainException('Cart is empty.'));

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('error', $result['status']);
        $this->assertSame(422, $result['code']);
        $this->assertSame('Cart is empty.', $result['message']);
    }

    public function testHandleReturnsCartDataOnConflictWhenUserIsAuthenticated(): void
    {
        $this->setupCreateOrderThrows(new ConflictException('Cart updated.'));

        $user = $this->createMock(User::class);
        $cartData = ['html' => '<div>cart</div>', 'totalItems' => 1];

        $this->securityProvider->method('getCurrentUser')->willReturn($user);

        $this->cartRenderQuery
            ->expects($this->once())
            ->method('buildCartResponse')
            ->with($user, '')
            ->willReturn($cartData);

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('error', $result['status']);
        $this->assertSame(409, $result['code']);
        $this->assertSame($cartData, $result['cart']);
    }

    public function testHandleReturnsNoCartDataOnConflictWhenUserIsNull(): void
    {
        $this->setupCreateOrderThrows(new ConflictException('Cart updated.'));

        $this->securityProvider->method('getCurrentUser')->willReturn(null);

        $this->cartRenderQuery->expects($this->never())->method('buildCartResponse');

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('error', $result['status']);
        $this->assertSame(409, $result['code']);
        $this->assertArrayNotHasKey('cart', $result);
    }

    public function testHandleLogsErrorWhenUnexpectedExceptionOccurs(): void
    {
        $this->setupCreateOrderThrows(new \RuntimeException('Unexpected failure.'));

        $this->logger->expects($this->once())->method('error');

        $result = $this->handler->handle($this->buildPayload());

        $this->assertSame('error', $result['status']);
        $this->assertSame(500, $result['code']);
    }

    private function initMocks(): void
    {
        $this->securityPolicy = $this->createMock(SecurityPolicyContract::class);
        $this->securityProvider = $this->createMock(SecurityProviderContract::class);
        $this->cartRenderQuery = $this->createMock(CartRenderQueryContract::class);
        $this->orderMutationCommand = $this->createMock(OrderMutationCommandContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new CreateOrderHandler(
            $this->securityPolicy,
            $this->securityProvider,
            $this->cartRenderQuery,
            $this->orderMutationCommand,
            $this->logger,
        );
    }

    private function buildPayload(OrderPaymentMethod $paymentMethod = OrderPaymentMethod::CASH): OrderCreatePayload {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  false,
            paymentMethod: $paymentMethod,
            billing:       new OrderBillingObject(1, 'Main St 1', '12345', 'Bratislava'),
        );
    }

    private function setupVerifiedUser(): void
    {
        $this->securityPolicy
            ->method('checkIfEmailVerified')
            ->willReturn($this->createMock(User::class));
    }

    private function setupCreateOrderThrows(\Throwable $exception): void
    {
        $this->setupVerifiedUser();

        $this->orderMutationCommand
            ->method('createOrder')
            ->willThrowException($exception);
    }

    private function setupCashOrderResult(int $orderId): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn($orderId);

        $this->orderMutationCommand
            ->method('createOrder')
            ->willReturn(new OrderResultObject(order: $order, paymentUrl: null));
    }
}
