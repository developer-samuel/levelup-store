<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject\Catalog;

use App\Core\Domain\Segment\Product\ValueObject\ProductVariantObject;

final readonly class ProductCatalogObject
{
    /**
     * @param ProductVariantObject[] $variants
     * @param array<int, string[]> $sortOptions
    */
    public function __construct(
        public bool $isDiscountRoute,
        public ProductCatalogFilterObject $filter,
        public ProductCatalogPaginationObject $pagination,
        public array $variants,
        public array $sortOptions,
        public string $sort,
    ) {}
}

