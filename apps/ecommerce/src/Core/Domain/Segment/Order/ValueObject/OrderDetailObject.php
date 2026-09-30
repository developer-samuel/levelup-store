<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderStatus
};

/**
 * @phpstan-type ObjectArray array{
 *     order: Order,
 *     totalPrice: float,
 *     statuses: OrderStatus[],
 *     items: OrderItemObject[],
 *     personal: array<string, mixed>,
 *     billing: array<string, mixed>,
 *     shipping: array<string, mixed>|null,
 *     pdfEnabled: bool
 * }
*/
final readonly class OrderDetailObject
{
    /**
     * @param Order $order
     * @param float $totalPrice
     * @param OrderStatus[] $statuses
     * @param OrderItemObject[] $items
     * @param array<string, mixed> $personal
     * @param array<string, mixed> $billing
     * @param array<string, mixed>|null $shipping
     * @param bool $pdfEnabled
    */
    public function __construct(
        public Order $order,
        public float $totalPrice,
        public array $statuses,
        public array $items,
        public array $personal,
        public array $billing,
        public ?array $shipping = null,
        public bool $pdfEnabled = false,
    ) {}

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'order'      => $this->order,
            'totalPrice' => $this->totalPrice,
            'statuses'   => $this->statuses,
            'items'      => $this->items,
            'personal'   => $this->personal,
            'billing'    => $this->billing,
            'shipping'   => $this->shipping,
            'pdfEnabled' => $this->pdfEnabled,
        ];
    }
}
