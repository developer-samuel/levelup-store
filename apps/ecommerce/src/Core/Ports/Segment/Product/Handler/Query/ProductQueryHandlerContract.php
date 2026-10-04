<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Handler\Query;

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

interface ProductQueryHandlerContract
{
    /** @return array<string, mixed> */
    public function handle(
        ProductFilterObject $filter,
        int $currentPage = 1,
        ProductSortOption $sort = ProductSortOption::TOP_RATED,
    ): array;
}
