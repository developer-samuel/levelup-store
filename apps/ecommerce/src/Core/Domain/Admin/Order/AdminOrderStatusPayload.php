<?php

declare(strict_types=1);

namespace App\Core\Domain\Admin\Order;

use App\Core\Domain\Segment\Order\Enum\OrderStatus;

final readonly class AdminOrderStatusPayload
{
    public function __construct(
        public string $code,
        public OrderStatus $status,
    ) {}
}
