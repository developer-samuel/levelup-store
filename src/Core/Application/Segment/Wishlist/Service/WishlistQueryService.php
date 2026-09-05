<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Wishlist\Service;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User,
    Segment\Wishlist\Entity\Wishlist
};

use App\Core\Application\Segment\Product\Resource\ProductVariantResource;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Review\Service\Query\ReviewQueryContract,
    Segment\Wishlist\Service\WishlistQueryContract,
    Segment\Wishlist\WishlistRepositoryContract
};

final readonly class WishlistQueryService implements WishlistQueryContract
{
    /**
     * @param SecurityProviderContract $securityProvider,
     * @param WishlistRepositoryContract $wishlistRepository
     * @param ReviewQueryContract $reviewQuery
    */
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private WishlistRepositoryContract $wishlistRepository,
        private ReviewQueryContract $reviewQuery,
    ) {}

    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function exists(User $user, ProductVariant $variant): bool
    {
        return $this->wishlistRepository->exists($user, $variant);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function inCurrentUserWishlist(ProductVariant $variant): bool
    {
        $currentUser = $this->securityProvider->getCurrentUser();

        return $currentUser instanceof User && $this->exists($currentUser, $variant);
    }

    /**
     * @param User $user
     *
     * @return list<array<string, mixed>>
    */
    public function fetchAllForUser(User $user): array
    {
        $wishlists = $this->wishlistRepository->findAllByUser($user);

        return array_values(
            array_map(
                function (Wishlist $wishlist): array {
                    $variant = $wishlist->getVariant();
                    $averageRating = $this->reviewQuery->getAverageRatingByVariant($variant->getId());

                    return ProductVariantResource::toArray($variant, $averageRating);
                },
                $wishlists,
            ),
        );
    }
}
