<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order
};

interface OrderItemCommandContract
{
    /** @param CartItem[] $cartItems */
    public function processOrderItems(Order $order, array $cartItems): void;

    /** @param CartItem[] $cartItems */
    public function validateAllItemsInStock(array $cartItems): void;
}
