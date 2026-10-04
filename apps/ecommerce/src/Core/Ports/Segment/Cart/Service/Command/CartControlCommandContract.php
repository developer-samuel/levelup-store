<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\User\Entity\User
};

interface CartControlCommandContract
{
    public function clearCart(Cart $cart): void;
    public function flushAndRefreshCart(Cart $cart): void;
    public function createNewCart(User $user): Cart;
}
