<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Notifier;

use App\Core\Domain\Segment\Order\Entity\Order;

interface OrderNotifierContract
{
    public function notifyPlaced(Order $order): void;
    public function notifyCompleted(Order $order): void;
    public function notifyRefunded(Order $order): void;
}
