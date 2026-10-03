<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Cart;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Cart\Entity\Cart;

final class CartAssertion
{
    /** @phpstan-assert Cart $cart */
    public static function assertExists(?Cart $cart): void
    {
        ExistenceAssertion::assertExists($cart, 'Cart');
    }
}
