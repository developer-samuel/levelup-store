<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject\Catalog;

use App\Core\Domain\Segment\Product\ValueObject\ProductPaginationObject;

final readonly class ProductCatalogPaginationObject
{
    /**
     * @param ProductPaginationObject $pagination
     * @param int $currentPage
     * @param int $maxPages
     * @param int $totalCount
     * @param bool $showLoadMore
    */
    public function __construct(
        public ProductPaginationObject $pagination,
        public int $currentPage,
        public int $maxPages,
        public int $totalCount,
        public bool $showLoadMore,
    ) {}
}

