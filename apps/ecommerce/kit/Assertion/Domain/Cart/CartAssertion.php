<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Cart;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Cart\Entity\Cart;

final class CartAssertion
{
    /**
     * @param Cart|null $cart
     *
     * @return void
     *
     * @throws NotFoundHttpException
     *
     * @phpstan-assert Cart $cart
    */
    public static function assertExists(?Cart $cart): void
    {
        ExistenceAssertion::assertExists($cart, 'Cart');
    }
}
