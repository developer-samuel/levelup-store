<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantStock;

interface ProductVariantStockRepositoryContract
{
    /**
     * @return ProductVariantStock[]
    */
    public function findAll(): array;
}
