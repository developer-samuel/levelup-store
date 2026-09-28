<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist\Service;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

interface WishlistQueryContract
{
    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function exists(User $user, ProductVariant $variant): bool;

    /**
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function inCurrentUserWishlist(ProductVariant $variant): bool;

    /**
     * @param User $user
     *
     * @return array<array<string, mixed>>
    */
    public function fetchAllForUser(User $user): array;
}
