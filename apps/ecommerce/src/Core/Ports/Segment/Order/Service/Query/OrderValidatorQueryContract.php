<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\User\Entity\User
};

/**
 * @phpstan-type CartItemsResult array{
 *     cart: Cart|null,
 *     items: CartItem[]
 * }
*/
interface OrderValidatorQueryContract
{
    /** @return CartItem[] */
    public function getCartItemsOrFail(User $user): array;

    /** @return CartItemsResult */
    public function validateUserAndGetCartItems(User $user): array;

    public function validateBillingData(OrderBillingObject $billing): void;
    public function validateShippingData(?OrderShippingObject $shipping): void;
}
