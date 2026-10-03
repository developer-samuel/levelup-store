<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Cart\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem
};

use App\Core\Ports\Segment\Cart\Repository\CartItemRepositoryContract;

/** @extends ServiceEntityRepository<CartItem> */
final class CartItemRepository extends ServiceEntityRepository implements CartItemRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            CartItem::class,
        );
    }

    public function getItem(int $itemId): ?CartItem
    {
        return $this->find($itemId);
    }

    /** @return CartItem[] */
    public function findByCart(Cart $cart): array
    {
        return $this->findBy(['cart' => $cart]);
    }

    /** @return CartItem[] */
    public function findAllWithVariant(): array
    {
        /** @var CartItem[] $result */
        $result = $this->createQueryBuilder('ci')
            ->join('ci.variant', 'v')
            ->addSelect('v')
            ->leftJoin('v.stock', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
