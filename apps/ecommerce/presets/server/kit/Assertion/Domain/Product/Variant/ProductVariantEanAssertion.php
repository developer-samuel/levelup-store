<?php

declare(strict_types=1);

namespace Presets\Kit\Assertion\Domain\Product\Variant;

use Presets\Kit\Assertion\Shared\ExistenceAssertion;

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
