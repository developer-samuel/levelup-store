<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist\Service;

use App\Core\Domain\Segment\User\Entity\User;

interface WishlistCommandContract
{
    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function toggle(User $user, int $variantId): bool;

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function remove(User $user, int $variantId): bool;
}
