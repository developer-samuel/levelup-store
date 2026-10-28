<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantRecommended
};

/**
 * @phpstan-type ResourceArray array{
 *     variant_id: int|string,
 *     name: string,
 *     imagePath: string,
 *     url: string,
 *     price: float,
 *     discountPrice: float|null,
 *     discount: bool,
 *     averageRating: float
 * }
 * @phpstan-type PriceData array{
 *     price: float,
 *     discountPrice: float|null,
 *     hasDiscount: bool
 * }
*/
final class ProductRecommendedResource
{
    /** @return ResourceArray */
    public static function toArray(ProductVariantRecommended $variantRecommended, float $averageRating = 0): array
    {
        $variant = $variantRecommended->getVariant();

        $priceData = self::getPriceData($variant);

        return [
            'variant_id'    => $variant->getId(),
            'name'          => $variant->getName(),
            'imagePath'     => self::resolveImagePath($variant),
            'url'           => $variant->getUrl(),
            'price'         => $priceData['price'],
            'discountPrice' => $priceData['discountPrice'],
            'discount'      => $priceData['hasDiscount'],
            'averageRating' => $averageRating,
        ];
    }

    /** @return PriceData */
    private static function getPriceData(ProductVariant $variant): array
    {
        $priceData = ProductVariantPriceResource::extract($variant);

        return [
            'price'         => $priceData['price'],
            'discountPrice' => $priceData['discountPrice'],
            'hasDiscount'   => $priceData['hasDiscount'],
        ];
    }

    private static function resolveImagePath(ProductVariant $variant): string
    {
        $image = $variant->getImage();

        if ($image === null) {
            return 'img/misc/image/no-image.webp';
        }

        return 'uploads/' . $image->getPath();
    }
}
