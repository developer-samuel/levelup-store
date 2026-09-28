<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Product\Variant;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariantEan
};

final class ProductVariantEanAssertion
{
    /**
     * @param ProductVariantEan|null $ean
     *
     * @return void
     *
     * @throws NotFoundHttpException
     *
     * @phpstan-assert ProductVariantEan $ean
    */
    public static function assertExists(?ProductVariantEan $ean): void
    {
        ExistenceAssertion::assertExists($ean, 'EAN');
    }
}
