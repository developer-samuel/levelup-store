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
    /** @return ProductVariantEan[] */
    public function findAllByVariantAndStatus(ProductVariant $variant, ProductVariantEanStatus $status): array;

    /** @return ProductVariantEan[] */
    public function findAvailableByVariant(ProductVariant $variant): array;

    public function findById(int $id): ?ProductVariantEan;
    public function existsByCode(string $code): bool;
}
