<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Notifier;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderStatusNotifierContract
{
    public function send(Order $order): void;
}
