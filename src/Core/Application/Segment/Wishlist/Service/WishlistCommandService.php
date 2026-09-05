<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Wishlist\Service;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User,
    Segment\Wishlist\Entity\Wishlist
};

use App\Core\Ports\{
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Segment\Wishlist\Service\WishlistCommandContract,
    Segment\Wishlist\Service\WishlistQueryContract,
    Segment\Wishlist\WishlistRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class WishlistCommandService implements WishlistCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param ProductVariantRepositoryContract $variantRepository
     * @param WishlistRepositoryContract $wishlistRepository
     * @param WishlistQueryContract $wishlistQuery
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private ProductVariantRepositoryContract $variantRepository,
        private WishlistRepositoryContract $wishlistRepository,
        private WishlistQueryContract $wishlistQuery,
    ) {}

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function toggle(User $user, int $variantId): bool
    {
        $variant = $this->variantRepository->findById($variantId);
        if (!$variant) {
            return false;
        }

        if ($this->wishlistQuery->exists($user, $variant)) {
            $this->remove($user, $variantId);
            return false;
        }

        $this->add($user, $variant);

        return true;
    }

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return bool
    */
    public function remove(User $user, int $variantId): bool
    {
        $variant = $this->variantRepository->findById($variantId);
        if (!$variant) {
            return false;
        }

        $item = $this->wishlistRepository->findOneByUserAndVariant($user, $variant);

        if ($item !== null) {
            $this->entityPersistence->remove($item, true);
        }

        return true;
    }

    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return void
    */
    private function add(User $user, ProductVariant $variant): void
    {
        if ($this->wishlistQuery->exists($user, $variant)) {
            return;
        }

        $wishlist = (new Wishlist())
            ->setUser($user)
            ->setVariant($variant);

        $this->entityPersistence->persist($wishlist, true);
    }
}
