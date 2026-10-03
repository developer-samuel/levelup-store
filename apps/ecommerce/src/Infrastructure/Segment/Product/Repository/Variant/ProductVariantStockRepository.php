<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantStock;

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantStockRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/** @extends AbstractRepository<ProductVariantStock> */
final class ProductVariantStockRepository extends AbstractRepository implements ProductVariantStockRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantStock::class,
        );
    }

    protected function getAlias(): string
    {
        return 'vs';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'createdAt';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }
}
