<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

interface CartControlCommandContract
{
    /**
     * @param Cart $cart
     *
     * @return void
    */
    public function clearCart(Cart $cart): void;

    /**
     * @param Cart $cart
     *
     * @return void
    */
    public function flushAndRefreshCart(Cart $cart): void;

    /**
     * @param User $user
     *
     * @return Cart
    */
    public function createNewCart(User $user): Cart;
}
