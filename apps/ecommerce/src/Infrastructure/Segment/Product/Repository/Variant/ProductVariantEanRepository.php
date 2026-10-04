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

/** @extends AbstractRepository<ProductVariantEan> */
final class ProductVariantEanRepository extends AbstractRepository implements ProductVariantEanRepositoryContract
{
    use IterableQuery;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantEan::class,
        );
    }

    protected function getAlias(): string
    {
        return 'pve';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'code';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /** @return ProductVariantEan[] */
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

    /** @return ProductVariantEan[] */
    public function findAvailableByVariant(ProductVariant $variant): array
    {
        return $this->findAllByVariantAndStatus($variant, ProductVariantEanStatus::ACTIVE);
    }

    public function findById(int $id): ?ProductVariantEan
    {
        return $this->find($id);
    }

    public function existsByCode(string $code): bool
    {
        return $this->existsByField('code', $code);
    }
}
