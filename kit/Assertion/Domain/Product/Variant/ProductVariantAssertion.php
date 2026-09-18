<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Product\Variant;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

final class ProductVariantAssertion
{
    /**
     * @param ProductVariant|null $variant
     *
     * @return void
     *
     * @throws NotFoundHttpException
     *
     * @phpstan-assert ProductVariant $variant
    */
    public static function assertExists(?ProductVariant $variant): void
    {
        ExistenceAssertion::assertExists($variant, 'Variant');
    }

    /**
     * @param ProductVariant $variant
     *
     * @return void
     *
     * @throws NotFoundHttpException
    */
    public static function assertNameExists(ProductVariant $variant): void
    {
        $name = $variant->getProduct()->getName();
        if ($name === '') {
            throw new NotFoundHttpException('Product or name is null');
        }
    }
}
