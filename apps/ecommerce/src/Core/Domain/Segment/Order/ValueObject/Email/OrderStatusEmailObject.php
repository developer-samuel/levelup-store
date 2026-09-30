<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Email;

use App\Core\Domain\Segment\Order\Entity\Order;

/**
 * @phpstan-type ObjectArray array{
 *     order: Order,
 *     url: string
 * }
*/
final readonly class OrderStatusEmailObject
{
    /**
     * @param Order $order
     * @param string $url
     */
    public function __construct(
        public Order $order,
        public string $url,
    ) {}

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'order' => $this->order,
            'url'   => $this->url,
        ];
    }
}
