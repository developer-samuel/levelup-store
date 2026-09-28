<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderFetchQueryContract
{
    /**
     * @param string $code
     *
     * @return Order
    */
    public function getOrderByCodeOrFail(string $code): Order;
}
