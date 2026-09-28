<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Repository;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPersonal
};

interface OrderPersonalRepositoryContract
{
    /**
     * @param Order $order
     *
     * @return OrderPersonal|null
    */
    public function findOneByOrder(Order $order): ?OrderPersonal;
}
