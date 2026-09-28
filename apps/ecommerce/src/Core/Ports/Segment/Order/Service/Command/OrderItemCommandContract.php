<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Order\Entity\Order
};

interface OrderItemCommandContract
{
    /**
     * @param Order $order
     * @param CartItem[] $cartItems
     *
     * @return void
    */
    public function processOrderItems(Order $order, array $cartItems): void;

    /**
     * @param CartItem[] $cartItems
     *
     * @return void
    */
    public function validateAllItemsInStock(array $cartItems): void;
}
