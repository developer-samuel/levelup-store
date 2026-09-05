<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository\Variant;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantDescription;

use App\Core\Ports\Segment\Product\Repository\Variant\ProductVariantDescriptionRepositoryContract;

use App\Infrastructure\{
    Segment\Product\Repository\Variant\Abstract\AbstractVariantRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\MaxValue
};

/**
 * @extends AbstractVariantRepository<ProductVariantDescription>
*/
final class ProductVariantDescriptionRepository extends AbstractVariantRepository implements ProductVariantDescriptionRepositoryContract
{
    use MaxValue;

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantDescription::class,
        );
    }

    /**
     * @param int $id
     *
     * @return ProductVariantDescription|null
    */
    public function findById(int $id): ?ProductVariantDescription
    {
        return $this->find($id);
    }

    /**
     * @param int $variantId
     *
     * @return int
    */
    public function getMaxPositionByVariantId(int $variantId): int
    {
        return $this->getMaxValue('position', ['variant' => $variantId]);
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'pvd';
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
