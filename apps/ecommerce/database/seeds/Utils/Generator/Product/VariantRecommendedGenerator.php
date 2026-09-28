<?php

declare(strict_types=1);

namespace Database\Seeds\Utils\Generator\Product;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

final class VariantRecommendedGenerator
{
    /**
     * @param array<ProductVariant> $variants
     *
     * @return array<int, array{
     *     variant: ProductVariant,
     *     position: int
     * }>
    */
    public function fetchData(array $variants): array
    {
        $randomVariants = [];

        if ($variants === []) {
            return [];
        }

        shuffle($variants);
        $selectedVariants = array_slice($variants, 0, min(10, count($variants)));

        $position = 1;

        foreach ($selectedVariants as $variant) {
            $randomVariants[] = [
                'variant'    => $variant,
                'position'   => $position++,
            ];
        }

        return $randomVariants;
    }
}
