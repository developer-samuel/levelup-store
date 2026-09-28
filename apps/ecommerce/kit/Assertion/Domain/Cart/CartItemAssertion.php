<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\Cart;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\Cart\Entity\CartItem;

final class CartItemAssertion
{
    /**
     * @param CartItem|null $item
     *
     * @return void
     *
     * @throws NotFoundHttpException
     *
     * @phpstan-assert CartItem $item
    */
    public static function assertExists(?CartItem $item): void
    {
        ExistenceAssertion::assertExists($item, 'Cart item');
    }

    /**
     * @param CartItem[] $items
     *
     * @return void
     *
     * @throws \RuntimeException
    */
    public static function assertNotEmpty(array $items): void
    {
        if ($items === []) {
            throw new \RuntimeException('Cart is empty.');
        }
    }
}
