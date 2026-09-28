<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\Segment\Product\Enum\ProductSortOption;

/**
 * @phpstan-type ResourceArray array{
 *     value: string,
 *     label: string
 * }
*/
final class ProductSortResource
{
    /**
     * @param ProductSortOption $option
     *
     * @return ResourceArray
    */
    public static function toArray(ProductSortOption $option): array
    {
        return [
            'value' => $option->value,
            'label' => $option->getLabel(),
        ];
    }
}
