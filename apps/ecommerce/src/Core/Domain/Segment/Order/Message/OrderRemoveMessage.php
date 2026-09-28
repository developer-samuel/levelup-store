<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Message;

final readonly class OrderRemoveMessage
{
    /**
     * @param int $orderId
    */
    public function __construct(
        public int $orderId,
    ) {}
}
