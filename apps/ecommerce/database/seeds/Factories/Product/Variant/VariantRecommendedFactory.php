<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Product\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantRecommended
};

trait VariantRecommendedFactory
{
    /**
     * @param ProductVariant $variant
     * @param int $position
     *
     * @return ProductVariantRecommended
    */
    private function createVariant(ProductVariant $variant, int $position): ProductVariantRecommended
    {
        return (new ProductVariantRecommended())
            ->setVariant($variant)
            ->setPosition($position);
    }
}
