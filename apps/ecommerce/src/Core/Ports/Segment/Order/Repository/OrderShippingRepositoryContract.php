<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Repository;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderShipping
};

interface OrderShippingRepositoryContract
{
    /**
     * @param Order $order
     *
     * @return OrderShipping|null
    */
    public function findOneByOrder(Order $order): ?OrderShipping;
}
