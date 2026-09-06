<?php

declare(strict_types=1);

namespace App\Core\Application\Search\Factory;

use App\Core\Domain\{
    Search\SearchResultObject,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Utils\ProductToolkit,
};

final class SearchResultFactory
{
    /**
     * @param ProductVariant $variant
     * @param float $averageRating
     *
     * @return SearchResultObject
    */
    public static function create(ProductVariant $variant, float $averageRating = 0.0): SearchResultObject
    {
        $imagePath = ProductToolkit::getFirstImagePath($variant);
        $discountData = ProductToolkit::getDiscountData($variant);

        return new SearchResultObject(
            name: $variant->getName(),
            price: $variant->getPrice(),
            url: $variant->getUrl(),
            image: $imagePath,
            discountPrice: isset($discountData['discountPrice']) ? $discountData['discountPrice'] : null,
            hasDiscount: (bool) ($discountData['hasDiscount'] ?? false),
            averageRating: $averageRating,
        );
    }
}
