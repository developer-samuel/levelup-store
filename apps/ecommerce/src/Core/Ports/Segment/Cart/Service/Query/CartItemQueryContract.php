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
    /** @return CartItem[] */
    public function getItems(User $user): array;

    /** @return CartAndVariant */
    public function getCartAndVariant(User $user, int $variantId): array;
    public function getValidatedCartItem(int $itemId): CartItem;
    public function getAvailableEansCount(ProductVariant $variant): int;
    public function getExistingQuantity(Cart $cart, ProductVariant $variant): int;

    /** @return array<string, mixed> */
    public function buildCartResponse(User $user, CartAction $action): array;
}
