<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\IterableQuery
};

/**
 * @extends AbstractRepository<ProductVariantEan>
*/
final class ProductVariantEanRepository extends AbstractRepository implements ProductVariantEanRepositoryContract
{
    use IterableQuery;

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantEan::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'pve';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'code';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /**
     * @param ProductVariant $variant
     * @param ProductVariantEanStatus $status
     *
     * @return ProductVariantEan[]
    */
    public function findAllByVariantAndStatus(ProductVariant $variant, ProductVariantEanStatus $status): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.variant = :variant')
            ->andWhere('e.status = :status')
            ->setParameter('variant', $variant)
            ->setParameter('status', $status);

        $results = $this->getIterableResult($qb);

        return $this->iteratorCollection(
            $results,
            ProductVariantEan::class,
        );
    }

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariantEan[]
    */
    public function findAvailableByVariant(ProductVariant $variant): array
    {
        return $this->findAllByVariantAndStatus($variant, ProductVariantEanStatus::ACTIVE);
    }

    /**
     * @param int $id
     *
     * @return ProductVariantEan|null
    */
    public function findById(int $id): ?ProductVariantEan
    {
        return $this->find($id);
    }

    /**
     * @param string $code
     *
     * @return bool
    */
    public function existsByCode(string $code): bool
    {
        return $this->existsByField('code', $code);
    }
}
