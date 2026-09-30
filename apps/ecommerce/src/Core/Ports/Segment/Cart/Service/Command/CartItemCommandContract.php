<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Command;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

interface CartItemCommandContract
{
    /**
     * @param User $user
     * @param int $variantId
     *
     * @return array<string, mixed>
    */
    public function addProductToCart(User $user, int $variantId): array;

    /**
     * @param User $user
     * @param int $itemId
     *
     * @return array<string, mixed>
    */
    public function removeProductFromCart(User $user, int $itemId): array;

    /**
     * @param ProductVariant $variant
     * @param CartItem[] $cartItems
     *
     * @return void
    */
    public function removeVariant(ProductVariant $variant, array $cartItems): void;
}
