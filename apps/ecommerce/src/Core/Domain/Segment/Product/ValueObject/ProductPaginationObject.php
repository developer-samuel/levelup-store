<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductPaginationObject
{
    /**
     * @param int $currentPage
     * @param int $limit
     * @param int $offset
    */
    public function __construct(
        public int $currentPage,
        public int $limit,
        public int $offset,
    ) {}
}
