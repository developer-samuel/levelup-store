<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductVariantObject
};

interface ProductVariantQueryContract
{
    /**
     * @param ProductVariant[] $variants
     *
     * @return ProductVariantObject[]
    */
    public function mapVariantsToData(array $variants): array;

    /**
     * @param string $url
     *
     * @return ProductVariant|null
    */
    public function getVariantOrNull(string $url): ?ProductVariant;

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariant[]
    */
    public function getAllVariantsOrNull(ProductVariant $variant): array;
}
