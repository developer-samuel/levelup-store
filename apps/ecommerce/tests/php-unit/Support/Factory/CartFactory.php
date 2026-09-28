<?php

declare(strict_types=1);

namespace Tests\Support\Factory;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

trait CartFactory
{
    private function createAndPersistCart(User $user): Cart
    {
        $cart = new Cart($user);

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    private function createAndPersistCartItem(Cart $cart, ProductVariant $variant): CartItem
    {
        $item = new CartItem($cart, $variant);

        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }
}
