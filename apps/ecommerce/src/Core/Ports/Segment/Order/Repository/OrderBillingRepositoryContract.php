<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Repository;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling
};

interface OrderBillingRepositoryContract
{
    /**
     * @param Order $order
     *
     * @return OrderBilling|null
    */
    public function findOneByOrder(Order $order): ?OrderBilling;
}
