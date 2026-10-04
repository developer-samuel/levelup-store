<?php

declare(strict_types=1);

namespace Packages\Kit\Assertion\Domain\Cart;

use Packages\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Cart\Entity\CartItem;

final class CartItemAssertion
{
    /** @phpstan-assert CartItem $item */
    public static function assertExists(?CartItem $item): void
    {
        ExistenceAssertion::assertExists($item, 'Cart item');
    }

    /** @param CartItem[] $items */
    public static function assertNotEmpty(array $items): void
    {
        if ($items === []) {
            throw new \RuntimeException('Cart is empty.');
        }
    }
}
