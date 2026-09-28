<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Wishlist;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User,
    Segment\Wishlist\Entity\Wishlist
};

use App\Core\Ports\Segment\Wishlist\WishlistRepositoryContract;

/**
 * @extends ServiceEntityRepository<Wishlist>
*/
final class WishlistRepository extends ServiceEntityRepository implements WishlistRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Wishlist::class,
        );
    }

    /**
     * @param User $user
     *
     * @return Wishlist[]
    */
    public function findAllByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function exists(User $user, ProductVariant $variant): bool
    {
        return (bool) $this->count(['user' => $user, 'variant' => $variant]);
    }

    /**
     * @param User $user
     * @param ProductVariant $variant
     *
     * @return Wishlist|null
    */
    public function findOneByUserAndVariant(User $user, ProductVariant $variant): ?Wishlist
    {
        return $this->findOneBy(['user' => $user, 'variant' => $variant]);
    }
}
