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
    /** @return OrderItem[] */
    public function findByOrder(Order $order): array;

    public function hasPurchasedVariant(User $user, int $variantId): bool;
}
