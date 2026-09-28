<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

interface ProductVariantDescriptionRepositoryContract
{
    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariantDescription[]
    */
    public function findAllByVariant(ProductVariant $variant): array;

    /**
     * @param int $id
     *
     * @return ProductVariantDescription|null
    */
    public function findById(int $id): ?ProductVariantDescription;

    /**
     * @param int $variantId
     *
     * @return int
    */
    public function getMaxPositionByVariantId(int $variantId): int;
}
