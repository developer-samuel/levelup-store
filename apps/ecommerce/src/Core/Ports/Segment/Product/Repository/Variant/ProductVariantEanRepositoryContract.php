<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository\Variant;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Enum\Variant\ProductVariantEanStatus
};

interface ProductVariantEanRepositoryContract
{
    /**
     * @param ProductVariant $variant
     * @param ProductVariantEanStatus $status
     *
     * @return ProductVariantEan[]
    */
    public function findAllByVariantAndStatus(ProductVariant $variant, ProductVariantEanStatus $status): array;

    /**
     * @param ProductVariant $variant
     *
     * @return ProductVariantEan[]
    */
    public function findAvailableByVariant(ProductVariant $variant): array;

    /**
     * @param int $id
     *
     * @return ProductVariantEan|null
    */
    public function findById(int $id): ?ProductVariantEan;

    /**
     * @param string $code
     *
     * @return bool
    */
    public function existsByCode(string $code): bool;
}
