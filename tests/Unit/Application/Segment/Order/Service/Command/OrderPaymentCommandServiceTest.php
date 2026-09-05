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
    Segment\Order\Entity\OrderPayment,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Builder\OrderCommandBuilder,
    Segment\Order\Builder\OrderQueryBuilder,
    Segment\Order\Service\Command\OrderPaymentCommandService
};

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Order\Notifier\OrderConfirmationNotifierContract,
    Segment\Order\Service\Command\OrderBuildCommandContract,
    Segment\Order\Service\Command\OrderCacheCommandContract,
    Segment\Order\Service\Command\OrderDataCommandContract,
    Segment\Order\Service\Command\OrderItemCommandContract,
    Segment\Order\Service\Command\OrderPaymentCommandContract,
    Segment\Order\Service\Command\OrderPreparationCommandContract,
    Segment\Order\Service\Query\OrderCacheQueryContract,
    Segment\Order\Service\Query\OrderCountryQueryContract,
    Segment\Order\Service\Query\OrderItemQueryContract,
    Segment\Order\Service\Query\OrderPaymentQueryContract,
    Segment\Order\Service\Query\OrderPreparationQueryContract,
    Segment\Order\Service\Query\OrderPriceQueryContract,
    Segment\Order\Service\Query\OrderValidatorQueryContract,
    Shared\Logging\AppLoggerContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Command\OrderPaymentCommandService
*/
class OrderPaymentCommandServiceTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private SecurityPolicyContract&MockObject $securityPolicy;
    private OrderBuildCommandContract&MockObject $orderBuildCommand;
    private OrderConfirmationNotifierContract&MockObject $notifier;
    private AppLoggerContract&MockObject $logger;
    private OrderPaymentQueryContract&MockObject $orderPaymentQuery;
    private OrderValidatorQueryContract&MockObject $orderValidatorQuery;
    private OrderItemCommandContract&MockObject $orderItemCommand;
    private OrderPaymentCommandService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderPaymentCommandContract::class, $this->service);
    }

    public function testProcessSuccessReturnsOrder(): void
    {
        $order = $this->setupPaymentProcessing(shouldProcessPayment: false);

        $result = $this->service->processSuccess('sess_abc');

        $this->assertSame($order, $result);
    }

    public function testProcessSuccessSendsNotification(): void
    {
        $this->setupPaymentProcessing(shouldProcessPayment: false);

        $this->notifier
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Order::class));

        $this->service->processSuccess('sess_abc');
    }

    public function testProcessSuccessCallsBuildCommandWithUserPayloadAndItems(): void
    {
        $user = $this->setupVerifiedUser();
        $payload = $this->buildPayload();
        $items = [$this->createMock(CartItem::class)];

        $this->orderPaymentQuery->method('retrieveCheckoutSession')->willReturn($this->buildSession());
        $this->orderPaymentQuery->method('extractPayloadFromMetadata')->willReturn($payload);
        $this->orderValidatorQuery->method('getCartItemsOrFail')->willReturn($items);
        $this->orderPaymentQuery->method('shouldProcessPayment')->willReturn(false);

        $order = $this->createMock(Order::class);
        $order->method('getUser')->willReturn($user);

        $this->orderBuildCommand
            ->expects($this->once())
            ->method('build')
            ->with($user, $payload, $items)
            ->willReturn($order);

        $this->notifier->method('send');

        $this->service->processSuccess('sess_abc');
    }

    public function testProcessSuccessPersistsOrderPaymentWhenShouldProcess(): void
    {
        $this->setupPaymentProcessing(shouldProcessPayment: true, paymentIntent: 'pi_xyz');

        $persisted = $this->capturePersistedOnProcessSuccess();

        $this->assertNotEmpty(array_filter($persisted, fn($e) => $e instanceof OrderPayment));
    }

    public function testProcessSuccessSkipsOrderPaymentWhenShouldNotProcess(): void
    {
        $this->setupPaymentProcessing(shouldProcessPayment: false);

        $persisted = $this->capturePersistedOnProcessSuccess();

        $this->assertEmpty(array_filter($persisted, fn($e) => $e instanceof OrderPayment));
    }

    public function testProcessSuccessThrowsLogicExceptionAndLogsCriticalOnFailure(): void
    {
        $this->setupVerifiedUser();
        $this->orderPaymentQuery
            ->method('retrieveCheckoutSession')
            ->willThrowException(new \RuntimeException('Stripe API down'));

        $this->logger
            ->expects($this->once())
            ->method('critical')
            ->with($this->stringContains('Failed to process payment'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred while processing the payment.');

        $this->service->processSuccess('sess_abc');
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->securityPolicy = $this->createMock(SecurityPolicyContract::class);
        $this->orderBuildCommand = $this->createMock(OrderBuildCommandContract::class);
        $this->notifier = $this->createMock(OrderConfirmationNotifierContract::class);
        $this->logger = $this->createMock(AppLoggerContract::class);
        $this->orderPaymentQuery = $this->createMock(OrderPaymentQueryContract::class);
        $this->orderValidatorQuery = $this->createMock(OrderValidatorQueryContract::class);
        $this->orderItemCommand = $this->createMock(OrderItemCommandContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderPaymentCommandService(
            entityPersistence:   $this->entityPersistence,
            securityPolicy:      $this->securityPolicy,
            orderQueryBuilder:   $this->buildQueryBuilder(),
            orderCommandBuilder: $this->buildCommandBuilder(),
            orderBuildCommand:   $this->orderBuildCommand,
            notifier:            $this->notifier,
            logger:              $this->logger,
        );
    }

    private function buildQueryBuilder(): OrderQueryBuilder
    {
        return new OrderQueryBuilder(
            orderCountryQuery:     $this->createMock(OrderCountryQueryContract::class),
            orderPreparationQuery: $this->createMock(OrderPreparationQueryContract::class),
            orderItemQuery:        $this->createMock(OrderItemQueryContract::class),
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

    private function setupPaymentProcessing(
        bool $shouldProcessPayment = false,
        ?string $paymentIntent = null,
    ): Order&MockObject {
        $user = $this->setupVerifiedUser();
        $order = $this->createMock(Order::class);
        $order->method('getUser')->willReturn($user);

        $this->orderPaymentQuery->method('retrieveCheckoutSession')->willReturn(
            $this->buildSession(paymentIntent: $paymentIntent ?? 'pi_abc'),
        );
        $this->orderPaymentQuery->method('extractPayloadFromMetadata')->willReturn($this->buildPayload());
        $this->orderValidatorQuery->method('getCartItemsOrFail')->willReturn([$this->createMock(CartItem::class)]);
        $this->orderBuildCommand->method('build')->willReturn($order);
        $this->orderPaymentQuery->method('shouldProcessPayment')->willReturn($shouldProcessPayment);
        $this->orderItemCommand->method('processOrderItems');
        $this->notifier->method('send');

        return $order;
    }

    private function setupVerifiedUser(): User&MockObject
    {
        $user = $this->buildUserMock();
        $this->securityPolicy->method('checkIfEmailVerified')->willReturn($user);

        return $user;
    }

    private function buildUserMock(): User&MockObject
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        return $user;
    }

    private function buildPayload(): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  false,
            paymentMethod: OrderPaymentMethod::CARD,
            billing:       new OrderBillingObject(1, 'Main St 1', '12345', 'Bratislava'),
        );
    }

    private function buildSession(?string $paymentIntent = 'pi_abc123'): StripeCheckoutObject
    {
        return new StripeCheckoutObject(
            metadata:      ['order_id' => '1'],
            amountTotal:   9999,
            paymentIntent: $paymentIntent,
        );
    }

    /**
     * @return object[]
    */
    private function capturePersistedOnProcessSuccess(string $sessionId = 'sess_abc'): array
    {
        $persisted = [];

        $this->entityPersistence
            ->method('persist')
            ->willReturnCallback(function (object $entity) use (&$persisted): void {
                $persisted[] = $entity;
            });

        $this->service->processSuccess($sessionId);

        return $persisted;
    }
}
