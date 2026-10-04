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
    /** @return Wishlist[] */
    public function findAllByUser(User $user): array;

    public function exists(User $user, ProductVariant $variant): bool;
    public function findOneByUserAndVariant(User $user, ProductVariant $variant): ?Wishlist;
}
