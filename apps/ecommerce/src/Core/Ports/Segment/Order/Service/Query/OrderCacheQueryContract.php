<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\User\Entity\User
};

interface OrderCacheQueryContract
{
    /**
     * @param User|null $user
     *
     * @return Order[]
    */
    public function getOrders(?User $user): array;
}
