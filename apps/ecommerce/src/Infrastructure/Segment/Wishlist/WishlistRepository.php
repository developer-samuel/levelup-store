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

/** @extends ServiceEntityRepository<Wishlist> */
final class WishlistRepository extends ServiceEntityRepository implements WishlistRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Wishlist::class,
        );
    }

    /** @return Wishlist[] */
    public function findAllByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    public function exists(User $user, ProductVariant $variant): bool
    {
        return (bool) $this->count(['user' => $user, 'variant' => $variant]);
    }

    public function findOneByUserAndVariant(User $user, ProductVariant $variant): ?Wishlist
    {
        return $this->findOneBy(['user' => $user, 'variant' => $variant]);
    }
}
