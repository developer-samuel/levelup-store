<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Event;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantStock;

final readonly class ProductStockOutEvent
{
    public function __construct(
        public ProductVariantStock $stock,
    ) {}
}
