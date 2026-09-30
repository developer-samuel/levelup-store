<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\ValueObject\ProductPriceObject
};

interface ProductPriceQueryContract
{
    /**
     * @param ProductVariant $variant
     *
     * @return ProductPriceObject
    */
    public function getPrice(ProductVariant $variant): ProductPriceObject;
}
