<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Projection;

use App\Core\Domain\{
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject
};

interface ProductVariantProjectionQueryContract
{
    /**
     * @return array{ids: int[], total: int}
     */
    public function search(string $term, int $limit): array;

    /**
     * @return array{ids: int[], total: int}
     */
    public function filter(
        ProductFilterObject $filter,
        int $page,
        int $limit,
        ?ProductSortOption $sort,
    ): array;
}
