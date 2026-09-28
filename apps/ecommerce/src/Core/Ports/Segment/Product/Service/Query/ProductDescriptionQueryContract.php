<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

interface ProductDescriptionQueryContract
{
    /**
     * @param ProductVariant $variant
     *
     * @return array<int, array<string, mixed>>
    */
    public function getProductDescriptions(ProductVariant $variant): array;
}
