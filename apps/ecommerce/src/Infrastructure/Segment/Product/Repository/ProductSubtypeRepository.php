<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Product\Entity\ProductSubtype;

use App\Core\Ports\Segment\Product\Repository\ProductSubtypeRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\OrderedQuery
};

/** @extends AbstractRepository<ProductSubtype> */
final class ProductSubtypeRepository extends AbstractRepository implements ProductSubtypeRepositoryContract
{
    use OrderedQuery;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductSubtype::class,
        );
    }

    protected function getAlias(): string
    {
        return 'ps';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }

    /** @return ProductSubtype[] */
    public function findAllByProductId(int $productId): array
    {
        $qb = $this->createQueryBuilder('ps')
            ->andWhere('ps.product = :product')
            ->setParameter('product', $productId);

        /** @var ProductSubtype[] $results */
        $results = $this->getOrderedResults($qb, 'ps', ProductSubtype::class);

        return $results;
    }
}
