<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantImage;

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantImageRepositoryContract;

use App\Infrastructure\{
    Segment\Product\Repository\Variant\Abstract\AbstractVariantRepository,
    Shared\Enum\SortDirection
};

/**
 * @extends AbstractVariantRepository<ProductVariantImage>
*/
final class ProductVariantImageRepository extends AbstractVariantRepository implements ProductVariantImageRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantImage::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'pvi';
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
        return SortDirection::ASC;
    }
}
