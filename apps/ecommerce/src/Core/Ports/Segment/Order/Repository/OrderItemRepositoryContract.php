<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Repository;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderItem,
    Segment\User\Entity\User
};

interface OrderItemRepositoryContract
{
    /**
     * @param Order $order
     *
     * @return OrderItem[]
     */
    public function findByOrder(Order $order): array;

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function hasPurchasedVariant(User $user, int $variantId): bool;
}
