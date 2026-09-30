<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductPriceObject
{
    /**
     * @param float $originalPrice
     * @param float $discountedPrice
     * @param bool $hasDiscount
    */
    public function __construct(
        public float $originalPrice,
        public float $discountedPrice,
        public bool $hasDiscount,
    ) {}
}
