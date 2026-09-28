<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Cart\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\Segment\Cart\Entity\Cart;

use App\Core\Ports\Segment\Cart\Repository\CartRepositoryContract;

/**
 * @extends ServiceEntityRepository<Cart>
*/
final class CartRepository extends ServiceEntityRepository implements CartRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Cart::class,
        );
    }

    /**
     * @param int $userId
     *
     * @return Cart|null
    */
    public function findCartForUser(int $userId): ?Cart
    {
        return $this->findOneBy(['user' => $userId]);
    }

    /**
     * @param \DateTimeImmutable $threshold
     *
     * @return Cart[]
    */
    public function findInactiveSince(\DateTimeImmutable $threshold): array
    {
        /** @var Cart[] $result */
        $result = $this->createQueryBuilder('c')
            ->where('c.updatedAt < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return Cart[]
    */
    public function findAbandonedForReminder(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        /** @var Cart[] $result */
        $result = $this->createQueryBuilder('c')
            ->innerJoin('c.items', 'i')
            ->where('c.updatedAt < :from')
            ->andWhere('c.updatedAt > :to')
            ->andWhere('c.reminderSentAt IS NULL')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return Cart[]
    */
    public function findEmpty(): array
    {
        /** @var Cart[] $result */
        $result = $this->createQueryBuilder('c')
            ->leftJoin('c.items', 'i')
            ->having('COUNT(i.id) = 0')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
