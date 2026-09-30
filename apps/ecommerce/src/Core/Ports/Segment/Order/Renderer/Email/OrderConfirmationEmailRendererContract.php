<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Renderer\Email;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Entity\OrderBilling,
    Segment\Order\Entity\OrderPersonal,
    Segment\Order\Entity\OrderShipping,
    Segment\Order\ValueObject\Email\OrderItemEmailObject
};

interface OrderConfirmationEmailRendererContract
{
    /**
     * @param Order $order
     * @param OrderPersonal $personal
     * @param OrderBilling $billing
     * @param OrderShipping|null $shipping
     * @param OrderItemEmailObject[] $items
     *
     * @return string
    */
    public function renderOrderConfirmationEmail(
        Order $order,
        OrderPersonal $personal,
        OrderBilling $billing,
        ?OrderShipping $shipping,
        array $items,
    ): string;
}
