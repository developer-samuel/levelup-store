<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Event;

use App\Core\Domain\Segment\Order\Entity\Order;

final readonly class OrderStatusChangedEvent
{
    /**
     * @param Order $order
    */
    public function __construct(
        public Order $order,
    ) {}
}
