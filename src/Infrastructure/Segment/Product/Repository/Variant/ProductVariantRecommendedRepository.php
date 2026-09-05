<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantRecommended;

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantRecommendedRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/**
 * @extends AbstractRepository<ProductVariantRecommended>
*/
final class ProductVariantRecommendedRepository extends AbstractRepository implements ProductVariantRecommendedRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantRecommended::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'vh';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'position';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }
}
