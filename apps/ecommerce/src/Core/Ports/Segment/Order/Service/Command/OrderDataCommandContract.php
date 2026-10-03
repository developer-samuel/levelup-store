<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Payload\OrderCreatePayload
};

interface OrderDataCommandContract
{
    public function attachOrderData(Order $order, OrderCreatePayload $payload): void;
}
