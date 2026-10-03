<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject;

use App\Core\Domain\Segment\Order\Entity\Order;

final readonly class OrderResultObject
{
    public function __construct(
        public ?Order $order,
        public ?string $paymentUrl,
    ) {}
}
