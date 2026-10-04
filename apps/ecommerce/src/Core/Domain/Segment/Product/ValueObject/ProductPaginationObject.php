<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductPaginationObject
{
    public function __construct(
        public int $currentPage,
        public int $limit,
        public int $offset,
    ) {}
}
