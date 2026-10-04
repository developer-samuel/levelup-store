<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist\Service;

use App\Core\Domain\Segment\User\Entity\User;

interface WishlistCommandContract
{
    public function toggle(User $user, int $variantId): bool;
    public function remove(User $user, int $variantId): bool;
}
