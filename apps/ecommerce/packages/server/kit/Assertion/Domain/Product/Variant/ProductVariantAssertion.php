<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Product\Variant;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

final class ProductVariantAssertion
{
    /** @phpstan-assert ProductVariant $variant */
    public static function assertExists(?ProductVariant $variant): void
    {
        ExistenceAssertion::assertExists($variant, 'Variant');
    }

    public static function assertNameExists(ProductVariant $variant): void
    {
        $name = $variant->getProduct()->getName();
        if ($name === '') {
            throw new NotFoundHttpException('Product or name is null');
        }
    }
}
