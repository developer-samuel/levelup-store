<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Repository;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderPayment
};

interface OrderPaymentRepositoryContract
{
    public function getByOrder(Order $order): ?OrderPayment;
}
