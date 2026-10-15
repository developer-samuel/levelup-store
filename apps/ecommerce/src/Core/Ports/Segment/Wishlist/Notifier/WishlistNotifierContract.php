<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist\Notifier;

use App\Core\Domain\Segment\User\Entity\User;

interface WishlistNotifierContract
{
    public function notifyAdded(User $user, int $variantId): void;
    public function notifyRemoved(User $user, int $variantId): void;
}
