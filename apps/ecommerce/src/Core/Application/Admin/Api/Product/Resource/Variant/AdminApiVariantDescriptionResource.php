<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Product\Resource\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantDescription;

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     variantId: int,
 *     title: string|null,
 *     body: string,
 *     createdAt: string
 * }
*/
final class AdminApiVariantDescriptionResource
{
    /**
     * @param ProductVariantDescription $description
     *
     * @return ResourceArray
    */
    public static function toArray(ProductVariantDescription $description): array
    {
        return [
            'id'        => $description->getId(),
            'variantId' => $description->getVariant()->getId(),
            'title'     => $description->getTitle(),
            'body'      => $description->getBody(),
            'createdAt' => DateTimeFormatter::formatDMY($description->getCreatedAt()),
        ];
    }
}
