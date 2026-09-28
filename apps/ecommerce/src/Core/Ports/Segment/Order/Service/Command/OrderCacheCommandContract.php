<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface OrderCacheCommandContract
{
    /**
     * @param User $user
     *
     * @return void
    */
    public function invalidateOrdersCache(User $user): void;
}
