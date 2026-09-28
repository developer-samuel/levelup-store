<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Product\Variant;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariantDescription;

final class ProductVariantDescriptionAssertion
{
    /**
     * @param ProductVariantDescription|null $description
     *
     * @return void
     *
     * @throws NotFoundHttpException
     *
     * @phpstan-assert ProductVariantDescription $description
    */
    public static function assertExists(?ProductVariantDescription $description): void
    {
        ExistenceAssertion::assertExists($description, 'Description');
    }
}
