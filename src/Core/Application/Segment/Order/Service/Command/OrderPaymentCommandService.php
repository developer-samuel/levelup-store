<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use Kit\{
    Assertion\Domain\Order\OrderPaymentAssertion,
    Assertion\Shared\IdAssertion
};

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPayment,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Builder\OrderCommandBuilder,
    Segment\Order\Builder\OrderQueryBuilder
};

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Segment\Order\Notifier\OrderConfirmationNotifierContract,
    Segment\Order\Service\Command\OrderBuildCommandContract,
    Segment\Order\Service\Command\OrderPaymentCommandContract,
    Shared\Logging\AppLoggerContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class OrderPaymentCommandService implements OrderPaymentCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param SecurityPolicyContract $securityPolicy
     * @param OrderQueryBuilder $orderQueryBuilder
     * @param OrderCommandBuilder $orderCommandBuilder
     * @param OrderBuildCommandContract $orderBuildCommand
     * @param OrderConfirmationNotifierContract $notifier
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private SecurityPolicyContract $securityPolicy,
        private OrderQueryBuilder $orderQueryBuilder,
        private OrderCommandBuilder $orderCommandBuilder,
        private OrderBuildCommandContract $orderBuildCommand,
        private OrderConfirmationNotifierContract $notifier,
        private AppLoggerContract $logger,
    ) {}

    /**
     * @param string $sessionId
     *
     * @return Order
    */
    public function processSuccess(string $sessionId): Order
    {
        $user = $this->securityPolicy->checkIfEmailVerified();

        try {
            $session = $this->orderQueryBuilder->orderPaymentQuery->retrieveCheckoutSession($sessionId);
            $payload = $this->orderQueryBuilder->orderPaymentQuery->extractPayloadFromMetadata($session);
            $items = $this->orderQueryBuilder->orderValidatorQuery->getCartItemsOrFail($user);
            $amountTotal = $session->amountTotal;

            $order = $this->orderBuildCommand->build($user, $payload, $items);

            $this->persistOrderPayment($user, $order, $session, $amountTotal, $payload);

            $this->orderCommandBuilder->orderItemCommand->processOrderItems($order, $items);
            $this->entityPersistence->flush();

            $this->notifier->send($order);

            return $order;
        } catch (\Throwable $throwable) {
            $this->logger->critical(
                'Failed to process payment: ' . $throwable->getMessage(),
                $throwable,
                $user,
            );

            throw new \RuntimeException(
                'An error occurred while processing the payment.',
                0,
                $throwable,
            );
        }
    }

    /**
     * @param User $user
     * @param Order $order
     * @param StripeCheckoutObject $session
     * @param int $amountTotal
     * @param OrderCreatePayload $payload
     *
     * @return void
    */
    private function persistOrderPayment(
        User $user,
        Order $order,
        StripeCheckoutObject $session,
        int $amountTotal,
        OrderCreatePayload $payload,
    ): void {
        $userId = IdAssertion::assert($user->getId(), 'User ID', \LogicException::class);

        if (!$this->orderQueryBuilder->orderPaymentQuery->shouldProcessPayment($userId, $payload)) {
            return;
        }

        $paymentIntent = OrderPaymentAssertion::assertPaymentIntent($session->paymentIntent);

        $orderPayment = $this->createOrderPayment($order, $paymentIntent, $amountTotal);

        $this->entityPersistence->persist($orderPayment, true);
    }

    /**
     * @param Order $order
     * @param string $paymentIntent
     * @param int $amountTotal
     *
     * @return OrderPayment
    */
    private function createOrderPayment(Order $order, string $paymentIntent, int $amountTotal): OrderPayment
    {
        return (new OrderPayment())
            ->setOrder($order)
            ->setTransactionUnique($paymentIntent)
            ->setPrice($amountTotal / 100);
    }
}
