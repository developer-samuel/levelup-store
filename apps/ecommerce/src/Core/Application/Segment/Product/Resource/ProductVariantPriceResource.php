<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Resource;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

/**
 * @phpstan-type PriceData array{
 *     price: float,
 *     discount: float|null,
 *     discountPrice: float|null,
 *     hasDiscount: bool
 * }
 */
final class ProductVariantPriceResource
{
    /** @return PriceData */
    public static function extract(ProductVariant $variant): array
    {
        $discount = $variant->getDiscount()?->getPrice();

        return [
            'price'         => $variant->getPrice(),
            'discount'      => $discount,
            'discountPrice' => $discount !== null ? $variant->getDiscountedPrice() : null,
            'hasDiscount'   => $discount !== null,
        ];
    }
}
