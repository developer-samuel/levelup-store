<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Factory;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Utils\ProductToolkit,
    Segment\Product\ValueObject\ProductVariantObject
};

use App\Shared\Utils\Formatter\DateTimeFormatter;

final class ProductVariantFactory
{
    /**
     * @param ProductVariant $variant
     * @param array<string, float> $prices
     * @param float $averageRating
     *
     * @return ProductVariantObject
    */
    public function fromObject(
        ProductVariant $variant,
        array $prices,
        float $averageRating,
    ): ProductVariantObject {
        return new ProductVariantObject(
            variantId: $variant->getId(),
            price: $prices['price'],
            discountPrice: $prices['discountPrice'],
            imagePath: ProductToolkit::getFirstImagePath($variant),
            name: $variant->getName(),
            url: $variant->getUrl(),
            createdAt: DateTimeFormatter::format($variant->getCreatedAt()),
            discount: $prices['discount'],
            averageRating: $averageRating,
        );
    }
}
