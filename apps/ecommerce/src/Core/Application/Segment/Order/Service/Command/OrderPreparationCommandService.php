<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Enum\OrderStatus,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\User\Entity\User
};

use App\Core\Application\Shared\Utils\CodeGenerator;

use App\Core\Ports\{
    Segment\Order\Service\Command\OrderPreparationCommandContract,
    Segment\Order\Service\Query\OrderPreparationQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class OrderPreparationCommandService implements OrderPreparationCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param OrderPreparationQueryContract $orderPreparationQuery
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private OrderPreparationQueryContract $orderPreparationQuery,
    ) {}

    /**
     * @param User $user
     * @param OrderCreatePayload $payload
     *
     * @return Order
    */
    public function prepareOrder(User $user, OrderCreatePayload $payload): Order
    {
        $userId = $this->orderPreparationQuery->validateUserId($user);
        $cartSummary = $this->orderPreparationQuery->getCartSummary($userId);
        $totalPrice = $this->orderPreparationQuery->extractTotalPrice($cartSummary);

        $order = $this->initializeOrder($user, $totalPrice);

        $paymentMethod = $this->orderPreparationQuery->resolvePaymentMethod($payload);

        $this->populateOrderDetails($order, $paymentMethod, $payload);

        $this->entityPersistence->persist($order);

        return $order;
    }

    /**
     * @param User $user
     * @param float $totalPrice
     *
     * @return Order
    */
    private function initializeOrder(User $user, float $totalPrice): Order
    {
        return (new Order())
            ->setUser($user)
            ->setCode(CodeGenerator::generateUnique())
            ->setPrice($totalPrice);
    }

    /**
     * @param Order $order
     * @param OrderPaymentMethod $paymentMethod
     * @param OrderCreatePayload $payload
     *
     * @return void
    */
    private function populateOrderDetails(Order $order, OrderPaymentMethod $paymentMethod, OrderCreatePayload $payload): void
    {
        $order->setPayment($paymentMethod)
            ->setStatus(OrderStatus::PENDING)
            ->setSendShipping($payload->shouldSendShipping());
    }
}
