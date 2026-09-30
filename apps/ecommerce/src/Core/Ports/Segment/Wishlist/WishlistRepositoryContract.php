<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User,
    Segment\Wishlist\Entity\Wishlist
};

interface WishlistRepositoryContract
{
    /**
     * @param User $user
     *
     * @return Wishlist[]
    */
    public function findAllByUser(User $user): array;

    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function exists(User $user, ProductVariant $variant): bool;

    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return Wishlist|null
    */
    public function findOneByUserAndVariant(User $user, ProductVariant $variant): ?Wishlist;
}
