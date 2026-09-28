<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Utils\ProductToolkit
};

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     variant_id: int,
 *     discount: float|null,
 *     price: float,
 *     discountPrice: float,
 *     imagePath: string|null,
 *     name: string,
 *     url: string,
 *     created_at: string,
 *     averageRating: float|null
 * }
 * @phpstan-type PriceData array{
 *     price: float,
 *     discount: ?float,
 *     discountPrice: float
 * }
*/
final class ProductVariantResource
{
    /**
     * @param ProductVariant $variant
     * @param float|null $averageRating
     *
     * @return ResourceArray
    */
    public static function toArray(
        ProductVariant $variant,
        ?float $averageRating = null,
    ): array {
        $prices = self::extractPrices($variant);

        return [
            'variant_id'    => $variant->getId(),
            'discount'      => $prices['discount'],
            'price'         => $prices['price'],
            'discountPrice' => $prices['discountPrice'],
            'imagePath'     => ProductToolkit::getFirstImagePath($variant),
            'name'          => $variant->getName(),
            'url'           => $variant->getUrl(),
            'created_at'    => DateTimeFormatter::format($variant->getCreatedAt()),
            'averageRating' => $averageRating,
        ];
    }

    /**
     * @param ProductVariant $variant
     *
     * @return PriceData
    */
    public static function extractPrices(ProductVariant $variant): array
    {
        $price = $variant->getPrice();
        $discount = $variant->getDiscount()?->getPrice();
        $discountPrice = $discount !== null ? $price - $discount : $price;

        return [
            'price'         => $price,
            'discount'      => $discount,
            'discountPrice' => $discountPrice,
        ];
    }
}
