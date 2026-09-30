<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\ValueObject\ProductDetailObject
};

use App\Core\Application\Segment\Review\Resource\ReviewListResource;

/**
 * @phpstan-import-type ResourceArray from ReviewListResource
 *
 * @phpstan-type ProductDetailShape array{
 *     variant: ProductVariant,
 *     variants: array<int, ProductVariant>,
 *     stocks: ProductVariantStock,
 *     price: ProductPriceShape,
 *     reviewData: ResourceArray|null,
 *     descriptions: array<int, array<string, mixed>>,
 *     firstImage: string|null,
 *     wishlistExists: bool
 * }
 * @phpstan-type ProductPriceShape array{
 *     original: float,
 *     discounted: float|null,
 *     hasDiscount: bool
 * }
*/
final class ProductDetailResource
{
    /**
     * @param ProductDetailObject $detail
     *
     * @return ProductDetailShape
    */
    public static function toArray(ProductDetailObject $detail): array
    {
        /** @var array<int, ProductVariant> $variants */
        $variants = array_values($detail->variants);

        return [
            'variant'        => $detail->variant,
            'variants'       => $variants,
            'stocks'         => $detail->stocks,
            'price'          => self::priceData($detail),
            'reviewData'     => ReviewListResource::toArray($detail->reviewData),
            'descriptions'   => $detail->descriptions,
            'firstImage'     => $detail->firstImage,
            'wishlistExists' => $detail->wishlistExists,
        ];
    }

    /**
     * @param ProductDetailObject $detail
     *
     * @return ProductPriceShape
    */
    private static function priceData(ProductDetailObject $detail): array
    {
        return [
            'original'    => $detail->price->originalPrice,
            'discounted'  => $detail->price->discountedPrice,
            'hasDiscount' => $detail->price->hasDiscount,
        ];
    }
}
