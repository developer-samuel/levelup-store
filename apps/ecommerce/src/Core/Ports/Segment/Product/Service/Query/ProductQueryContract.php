<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

interface ProductQueryContract
{
    /**
     * @param ProductFilterObject $filter
     * @param int $page
     * @param ProductSortOption $sort
     *
     * @return array<string, mixed>
    */
    public function getFilteredAndSortedData(
        ProductFilterObject $filter,
        int $page = 1,
        ProductSortOption $sort = ProductSortOption::TOP_RATED,
    ): array;
}
