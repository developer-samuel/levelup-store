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

/** @extends AbstractVariantRepository<ProductVariantDescription> */
final class ProductVariantDescriptionRepository extends AbstractVariantRepository implements ProductVariantDescriptionRepositoryContract
{
    use MaxValue;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ProductVariantDescription::class,
        );
    }

    public function findById(int $id): ?ProductVariantDescription
    {
        return $this->find($id);
    }

    public function getMaxPositionByVariantId(int $variantId): int
    {
        return $this->getMaxValue('position', ['variant' => $variantId]);
    }

    protected function getAlias(): string
    {
        return 'pvd';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'position';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }
}
