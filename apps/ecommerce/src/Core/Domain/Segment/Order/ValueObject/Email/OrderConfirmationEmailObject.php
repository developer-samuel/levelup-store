<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Email;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping
};

/**
 * @phpstan-type ObjectArray array{
 *     order: Order,
 *     personal: OrderPersonal,
 *     billing: OrderBilling,
 *     shipping: OrderShipping|null,
 *     items: OrderItemEmailObject[]
 * }
*/
final readonly class OrderConfirmationEmailObject
{
    /**
     * @param Order $order
     * @param OrderPersonal $personal
     * @param OrderBilling $billing
     * @param OrderShipping|null $shipping
     * @param OrderItemEmailObject[] $items
     */
    public function __construct(
        public Order $order,
        public OrderPersonal $personal,
        public OrderBilling $billing,
        public ?OrderShipping $shipping,
        public array $items,
    ) {}

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'order'    => $this->order,
            'personal' => $this->personal,
            'billing'  => $this->billing,
            'shipping' => $this->shipping,
            'items'    => $this->items,
        ];
    }
}
