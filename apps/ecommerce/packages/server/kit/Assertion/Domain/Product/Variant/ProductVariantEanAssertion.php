<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Product\Variant;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariantEan
};

final class ProductVariantEanAssertion
{
    /** @phpstan-assert ProductVariantEan $ean */
    public static function assertExists(?ProductVariantEan $ean): void
    {
        ExistenceAssertion::assertExists($ean, 'EAN');
    }
}
