<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDescription
};

interface ProductVariantDescriptionRepositoryContract
{
    /** @return ProductVariantDescription[] */
    public function findAllByVariant(ProductVariant $variant): array;

    public function findById(int $id): ?ProductVariantDescription;
    public function getMaxPositionByVariantId(int $variantId): int;
}
