<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant\Abstract;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

use App\Infrastructure\Shared\Enum\SortDirection;

/**
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
*/
abstract class AbstractVariantRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     * @param class-string<T> $entityClass
    */
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
    ) {
        parent::__construct(
            $registry,
            $entityClass,
        );
    }

    /**
     * Finds all entities by variant.
     *
     * @param ProductVariant $variant
     *
     * @return T[]
    */
    final public function findAllByVariant(ProductVariant $variant): array
    {
        /** @var T[] $results */
        $results = $this->createQueryBuilder('v')
            ->andWhere('v.variant = :variant')
            ->setParameter('variant', $variant)
            ->orderBy('v.id', SortDirection::ASC->sort())
            ->getQuery()
            ->getResult();

        return $results;
    }
}
