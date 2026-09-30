<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderPaymentCommandContract
{
    /**
     * @param string $sessionId
     *
     * @return Order
    */
    public function processSuccess(string $sessionId): Order;
}
