<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Product\Variant;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantStock;

final class ProductVariantStockAssertion
{
    /** @phpstan-assert ProductVariantStock $stock */
    public static function assertExists(?ProductVariantStock $stock): void
    {
        ExistenceAssertion::assertExists($stock, 'Variant stock');
    }

    public static function assertStockQuantities(int $available, int $reserved): void
    {
        if ($available < 0 || $reserved < 0) {
            throw new \InvalidArgumentException("Stock quantities cannot be negative.");
        }
    }
}
