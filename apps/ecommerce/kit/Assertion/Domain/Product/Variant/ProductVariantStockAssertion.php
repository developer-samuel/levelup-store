<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Product\Variant;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantStock;

final class ProductVariantStockAssertion
{
    /**
     * @param ProductVariantStock|null $stock
     *
     * @return void
     *
     * @throws NotFoundHttpException
     *
     * @phpstan-assert ProductVariantStock $stock
    */
    public static function assertExists(?ProductVariantStock $stock): void
    {
        ExistenceAssertion::assertExists($stock, 'Variant stock');
    }

    /**
     * @param int $available
     * @param int $reserved
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public static function assertStockQuantities(int $available, int $reserved): void
    {
        if ($available < 0 || $reserved < 0) {
            throw new \InvalidArgumentException("Stock quantities cannot be negative.");
        }
    }
}
