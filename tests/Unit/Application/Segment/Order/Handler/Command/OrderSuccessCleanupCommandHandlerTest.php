<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Handler\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Order\Handler\Command\OrderSuccessCleanupCommandHandler;

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Query\CartControlQueryContract,
    Segment\Order\Handler\Command\OrderSuccessCleanupCommandHandlerContract,
    Segment\Order\Service\Command\OrderPaymentCommandContract,
    Shared\Logging\AppLoggerContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Handler\Command\OrderSuccessCleanupCommandHandler
*/
final class OrderSuccessCleanupCommandHandlerTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private CartControlQueryContract&MockObject $cartControlQuery;
    private CartControlCommandContract&MockObject $cartControlCommand;
    private OrderPaymentCommandContract&MockObject $orderPaymentCommand;
    private AppLoggerContract&MockObject $logger;
    private OrderSuccessCleanupCommandHandler $handler;
    private User&MockObject $user;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initHandler();

        $this->user = $this->createMock(User::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderSuccessCleanupCommandHandlerContract::class, $this->handler);
    }

    public function testHandleReturnsSuccessStatus(): void
    {
        $this->withNoCart();

        $result = $this->handler->handle(null, $this->user);

        $this->assertSame('success', $result['status']);
    }

    public function testHandleReturnsSuccessMessage(): void
    {
        $this->withNoCart();

        $result = $this->handler->handle(null, $this->user);

        $this->assertSame('Order cleaned successfully', $result['message']);
    }

    public function testHandleCallsProcessSuccessWhenSessionIdProvided(): void
    {
        $this->orderPaymentCommand
            ->expects($this->once())
            ->method('processSuccess')
            ->with('sess_abc');

        $this->withNoCart();

        $this->handler->handle('sess_abc', $this->user);
    }

    public function testHandleSkipsProcessSuccessWhenSessionIdIsNull(): void
    {
        $this->orderPaymentCommand
            ->expects($this->never())
            ->method('processSuccess');

        $this->withNoCart();

        $this->handler->handle(null, $this->user);
    }

    public function testHandleClearsCartWhenCartExists(): void
    {
        $cart = $this->createMock(Cart::class);
        $this->cartControlQuery->method('getUserCart')->willReturn($cart);

        $this->cartControlCommand
            ->expects($this->once())
            ->method('clearCart')
            ->with($cart);

        $this->handler->handle(null, $this->user);
    }

    public function testHandleFlushesWhenCartExists(): void
    {
        $this->cartControlQuery
            ->method('getUserCart')
            ->willReturn($this->createMock(Cart::class));

        $this->entityPersistence
            ->expects($this->once())
            ->method('flush');

        $this->handler->handle(null, $this->user);
    }

    public function testHandleDoesNotClearCartWhenCartIsNull(): void
    {
        $this->withNoCart();

        $this->cartControlCommand
            ->expects($this->never())
            ->method('clearCart');

        $this->entityPersistence
            ->expects($this->never())
            ->method('flush');

        $this->handler->handle(null, $this->user);
    }

    public function testHandleReturnsErrorWhenOrderPaymentCommandThrows(): void
    {
        $this->orderPaymentCommand
            ->method('processSuccess')
            ->willThrowException(new \LogicException('Payment processing failed.'));

        $result = $this->handler->handle('sess_abc', $this->user);

        $this->assertSame('error', $result['status']);
        $this->assertSame(500, $result['code']);
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->cartControlQuery = $this->createMock(CartControlQueryContract::class);
        $this->cartControlCommand = $this->createMock(CartControlCommandContract::class);
        $this->orderPaymentCommand = $this->createMock(OrderPaymentCommandContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
    }

    private function initHandler(): void
    {
        $this->handler = new OrderSuccessCleanupCommandHandler(
            $this->entityPersistence,
            $this->cartControlQuery,
            $this->cartControlCommand,
            $this->orderPaymentCommand,
            $this->logger,
        );
    }

    private function withNoCart(): void
    {
        $this->cartControlQuery
            ->method('getUserCart')
            ->willReturn(null);
    }
}
