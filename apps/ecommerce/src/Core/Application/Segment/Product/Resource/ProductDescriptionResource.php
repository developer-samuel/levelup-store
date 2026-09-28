<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantDescription;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     variant_id: int,
 *     position: int,
 *     title: string|null,
 *     body: string
 * }
*/
final class ProductDescriptionResource
{
    /**
     * @param ProductVariantDescription $description
     *
     * @return ResourceArray
    */
    public static function toArray(ProductVariantDescription $description): array
    {
        $variant = $description->getVariant();

        return [
            'id'         => $description->getId(),
            'variant_id' => $variant->getId(),
            'position'   => $description->getPosition(),
            'title'      => $description->getTitle(),
            'body'       => $description->getBody(),
        ];
    }
}
