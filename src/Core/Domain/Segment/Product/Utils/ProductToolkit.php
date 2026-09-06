<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Utils;

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Domain\Segment\Product\Entity\Variant\{
    ProductVariant,
    ProductVariantImage,
};

final class ProductToolkit
{
    /**
     * @param ProductVariant $variant
     *
     * @return string
    */
    public static function getFirstImagePath(ProductVariant $variant): string
    {
        $image = $variant->getImage();

        if ($image === null) {
            $first = $variant->getImages()->first();
            $image = $first instanceof ProductVariantImage ? $first : null;
        }

        if ($image === null) {
            return 'img/misc/image/no-image.webp';
        }

        return '/uploads/' . $image->getPath();
    }

    /**
     * @param ProductVariant $variant
     *
     * @return array{
     *     hasDiscount: bool,
     *     discountPrice: float|null
     * }
    */
    public static function getDiscountData(ProductVariant $variant): array
    {
        $discount = $variant->getDiscount();

        if ($discount === null) {
            return [
                'hasDiscount'   => false,
                'discountPrice' => null,
            ];
        }

        return [
            'hasDiscount'   => true,
            'discountPrice' => DataSanitizer::sanitizeFloat($discount->getPrice()),
        ];
    }
}
