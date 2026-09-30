<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Resource\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantEan;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     variantId: int,
 *     code: string,
 *     createdAt: string
 * }
*/
final class AdminApiVariantEanResource
{
    /**
     * @param ProductVariantEan $ean
     *
     * @return ResourceArray
    */
    public static function toArray(ProductVariantEan $ean): array
    {
        return [
            'id'        => $ean->getId(),
            'variantId' => $ean->getVariant()->getId(),
            'code'      => $ean->getCode(),
            'createdAt' => DateTimeFormatter::formatDMY($ean->getCreatedAt()),
        ];
    }
}
