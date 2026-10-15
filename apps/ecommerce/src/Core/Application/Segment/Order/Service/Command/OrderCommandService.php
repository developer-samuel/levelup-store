<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use Presets\Kit\Assertion\Domain\User\UserAssertion;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\OrderResultObject,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Builder\OrderCommandBuilder,
    Segment\Order\Builder\OrderQueryBuilder
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Notifier\OrderNotifierContract,
    Segment\Order\Notifier\OrderConfirmationNotifierContract,
    Segment\Order\Service\Command\OrderBuildCommandContract,
    Segment\Order\Service\Command\OrderCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class OrderCommandService implements OrderCommandContract
{
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private SecurityProviderContract $securityProvider,
        private OrderQueryBuilder $orderQueryBuilder,
        private OrderCommandBuilder $orderCommandBuilder,
        private OrderBuildCommandContract $orderBuildCommand,
        private OrderConfirmationNotifierContract $orderConfirmationNotifier,
        private OrderNotifierContract $orderNotifier,
    ) {}

    public function createOrder(OrderCreatePayload $payload): OrderResultObject
    {
        $user = $this->securityProvider->getCurrentUser();
        UserAssertion::assertExists($user);

        $items = $this->orderQueryBuilder->orderValidatorQuery->getCartItemsOrFail($user);

        $this->orderCommandBuilder->orderItemCommand->validateAllItemsInStock($items);

        if ($payload->paymentMethod === OrderPaymentMethod::CASH) {
            return $this->processCashOrder($user, $payload, $items);
        }

        return $this->initiateCardPayment($payload, $items);
    }

    /** @param CartItem[] $items */
    private function processCashOrder(User $user, OrderCreatePayload $payload, array $items): OrderResultObject
    {
        $order = $this->entityPersistence->wrapInTransaction(function () use ($user, $payload, $items) {
            $order = $this->orderBuildCommand->build($user, $payload, $items);

            $this->orderCommandBuilder->orderItemCommand->processOrderItems($order, $items);

            $this->orderConfirmationNotifier->send($order);
            $this->orderNotifier->notifyPlaced($order);

            return $order;
        });

        return new OrderResultObject(order: $order, paymentUrl: null);
    }

    /** @param CartItem[] $items */
    private function initiateCardPayment(OrderCreatePayload $payload, array $items): OrderResultObject
    {
        $lineItems = $this->orderQueryBuilder->orderItemQuery->prepareLineItems($items);

        $paymentUrl = $this->orderQueryBuilder->orderPaymentQuery->initiateCardPayment(
            $lineItems,
            $payload,
        );

        return new OrderResultObject(
            order: null,
            paymentUrl: $paymentUrl,
        );
    }
}
