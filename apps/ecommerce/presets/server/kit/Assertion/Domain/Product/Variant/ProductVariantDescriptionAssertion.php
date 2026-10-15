<?php

declare(strict_types=1);

namespace Presets\Kit\Assertion\Domain\Product\Variant;

use Presets\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantDescription;

final class ProductVariantDescriptionAssertion
{
    /** @phpstan-assert ProductVariantDescription $description */
    public static function assertExists(?ProductVariantDescription $description): void
    {
        ExistenceAssertion::assertExists($description, 'Description');
    }
}
