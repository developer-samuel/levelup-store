<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductFilterObject
{
    /**
     * @param bool $isDiscountRoute
     * @param string[] $subtypes
     * @param string[] $brands
     * @param string|null $category
     * @param string|null $type
     * @param float|null $minPrice
     * @param float|null $maxPrice
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
