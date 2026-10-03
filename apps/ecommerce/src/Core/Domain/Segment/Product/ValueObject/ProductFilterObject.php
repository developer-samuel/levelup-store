<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductFilterObject
{
    /**
     * @param string[] $subtypes
     * @param string[] $brands
    */
    public function __construct(
        public bool $isDiscountRoute,
        public array $subtypes,
        public array $brands,
        public ?string $category = null,
        public ?string $type = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
    ) {}
}
