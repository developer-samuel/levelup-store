<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Resource\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Enum\Variant\ProductVariantStatus
};

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     sku: string|null,
 *     name: string,
 *     price: float,
 *     discountedPrice: float,
 *     status: ProductVariantStatus,
 *     createdAt: string
 * }
*/
final class AdminApiVariantResource
{
    /**
     * @param ProductVariant $variant
     *
     * @return ResourceArray
    */
    public static function toArray(ProductVariant $variant): array
    {
        return [
            'id'              => $variant->getId(),
            'sku'             => $variant->getSku(),
            'name'            => $variant->getName(),
            'price'           => $variant->getPrice(),
            'discountedPrice' => $variant->getDiscountedPrice(),
            'status'          => $variant->getStatus(),
            'createdAt'       => DateTimeFormatter::formatDMY($variant->getCreatedAt()),
        ];
    }
}
