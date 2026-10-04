<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductPriceObject
{
    public function __construct(
        public float $originalPrice,
        public float $discountedPrice,
        public bool $hasDiscount,
    ) {}
}
