<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject\Catalog;

use App\Core\Domain\{
    Segment\Brand\Brand,
    Segment\Product\ValueObject\ProductVariantObject
};

final readonly class ProductCatalogFilterObject
{
    /**
     * @param string[] $types
     * @param string[] $subtypes
     * @param Brand[] $brands
     * @param float $maxPrice
     * @param ProductVariantObject[] $filtered
     * @param string|null $category
     * @param string|null $type
    */
    public function __construct(
        public array $types,
        public array $subtypes,
        public array $brands,
        public float $maxPrice,
        public array $filtered,
        public ?string $category,
        public ?string $type,
    ) {}
}

