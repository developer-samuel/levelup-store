<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\ValueObject;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

final readonly class ReviewListWithVariantObject
{
    /**
     * @param ReviewListObject $list
     * @param ProductVariant $variant
    */
    public function __construct(
        public ReviewListObject $list,
        public ProductVariant $variant,
    ) {}
}
