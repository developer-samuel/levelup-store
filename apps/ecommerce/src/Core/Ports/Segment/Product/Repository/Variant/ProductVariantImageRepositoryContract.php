<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantImage
};

interface ProductVariantImageRepositoryContract
{
    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariantImage[]
    */
    public function findAllByVariant(ProductVariant $variant): array;
}
