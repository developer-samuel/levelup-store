<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Event;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\ValueObject\Email\OrderItemEmailObject
};

final readonly class OrderConfirmationRequestedEvent
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
}
