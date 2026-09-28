<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Query;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Cart\Enum\CartAction,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

/**
 * @phpstan-type CartAndVariant array{
 *     cart: Cart,
 *     variant: ProductVariant
 * }
*/
interface CartItemQueryContract
{
    /**
     * @param User $user
     *
     * @return CartItem[]
    */
    public function getItems(User $user): array;

    /**
     * @param User $user
     * @param int $variantId
     *
     * @return CartAndVariant
    */
    public function getCartAndVariant(User $user, int $variantId): array;

    /**
     * @param int $itemId
     *
     * @return CartItem
    */
    public function getValidatedCartItem(int $itemId): CartItem;

    /**
     * @param ProductVariant $variant
     *
     * @return int
    */
    public function getAvailableEansCount(ProductVariant $variant): int;

    /**
     * @param Cart $cart
     * @param ProductVariant $variant
     *
     * @return int
    */
    public function getExistingQuantity(Cart $cart, ProductVariant $variant): int;

    /**
     * @param User $user
     * @param CartAction $action
     *
     * @return array<string, mixed>
    */
    public function buildCartResponse(User $user, CartAction $action): array;
}
