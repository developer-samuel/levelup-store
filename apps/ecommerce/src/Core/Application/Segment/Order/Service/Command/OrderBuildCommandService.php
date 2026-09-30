<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Builder\OrderCommandBuilder,
    Segment\Order\Builder\OrderQueryBuilder
};

use App\Core\Ports\{
    Segment\Order\Service\Command\OrderBuildCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class OrderBuildCommandService implements OrderBuildCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param OrderCommandBuilder $orderCommandBuilder
     * @param OrderQueryBuilder $orderQueryBuilder
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private OrderCommandBuilder $orderCommandBuilder,
        private OrderQueryBuilder $orderQueryBuilder,
    ) {}

    /**
     * @param User $user
     * @param OrderCreatePayload $payload
     * @param CartItem[] $items
     *
     * @return Order
    */
    public function build(User $user, OrderCreatePayload $payload, array $items): Order
    {
        $order = $this->orderCommandBuilder->orderPreparationCommand->prepareOrder($user, $payload);

        $this->orderCommandBuilder->orderDataCommand->attachOrderData($order, $payload);

        $lineItems = $this->orderQueryBuilder->orderItemQuery->prepareLineItems($items);
        $totalPrice = $this->orderQueryBuilder->orderPriceQuery->calculateTotalPrice($lineItems);

        $order->setPrice($totalPrice);

        $this->entityPersistence->flush();

        $this->orderCommandBuilder->orderCacheCommand->invalidateOrdersCache($user);

        return $order;
    }
}
