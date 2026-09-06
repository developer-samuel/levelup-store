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

/**
 * @extends AbstractRepository<ProductVariantStock>
*/
final class ProductVariantStockRepository extends AbstractRepository implements ProductVariantStockRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantStock::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'vs';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'createdAt';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }
}
