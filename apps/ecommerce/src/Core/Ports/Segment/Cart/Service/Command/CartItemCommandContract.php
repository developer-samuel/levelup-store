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
    /** @return array<string, mixed> */
    public function addProductToCart(User $user, int $variantId): array;

    /** @return array<string, mixed> */
    public function removeProductFromCart(User $user, int $itemId): array;

    /** @param CartItem[] $cartItems */
    public function removeVariant(ProductVariant $variant, array $cartItems): void;
}
