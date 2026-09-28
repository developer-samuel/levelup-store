<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Assembler;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductVariantObject
};

use App\Core\Ports\Segment\Review\Service\Query\ReviewQueryContract;

interface ProductVariantAssemblerContract
{
    /**
     * @param ProductVariant $variant
     * @param ReviewQueryContract $reviewQuery
     *
     * @return ProductVariantObject
    */
    public function toObject(
        ProductVariant $variant,
        ReviewQueryContract $reviewQuery,
    ): ProductVariantObject;
}
