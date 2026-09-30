<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Resource\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantImage;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     path: string,
 *     createdAt: string
 * }
*/
final class AdminApiVariantImageResource
{
    /**
     * @param ProductVariantImage $image
     *
     * @return ResourceArray
    */
    public static function toArray(ProductVariantImage $image): array
    {
        return [
            'id'        => $image->getId(),
            'path'      => $image->getPath(),
            'createdAt' => DateTimeFormatter::formatDMY($image->getCreatedAt()),
        ];
    }
}
