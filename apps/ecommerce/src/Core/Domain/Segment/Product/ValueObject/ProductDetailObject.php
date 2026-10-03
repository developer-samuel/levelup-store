<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Review\ValueObject\ReviewListObject
};

final readonly class ProductDetailObject
{
    /**
     * @param ProductVariant[] $variants
     * @param array<int, array<string, mixed>> $descriptions
    */
    public function __construct(
        public ProductVariant $variant,
        public array $variants,
        public ProductVariantStock $stocks,
        public ProductPriceObject $price,
        public array $descriptions,
        public bool $wishlistExists,
        public ?string $firstImage = null,
        public ?ReviewListObject $reviewData = null,
    ) {}
}
