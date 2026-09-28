<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductPriceObject
};

use App\Core\Ports\Segment\Product\Service\Query\ProductPriceQueryContract;

final class ProductPriceQueryService implements ProductPriceQueryContract
{
    /**
     * @param ProductVariant $variant
     *
     * @return ProductPriceObject
    */
    public function getPrice(ProductVariant $variant): ProductPriceObject
    {
        $originalPrice = $variant->getPrice();
        $discountedPrice = $variant->getDiscountedPrice();

        return new ProductPriceObject(
            originalPrice: $originalPrice,
            discountedPrice: $discountedPrice,
            hasDiscount: $originalPrice > $discountedPrice,
        );
    }
}
