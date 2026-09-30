<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Policy;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Product\Entity\Variant\ProductVariant
};

use App\Core\Ports\{
    Segment\Cart\Policy\CartItemAvailabilityPolicyContract,
    Segment\Cart\Service\Query\CartItemQueryContract
};

final readonly class CartItemAvailabilityPolicy implements CartItemAvailabilityPolicyContract
{
    /**
     * @param CartItemQueryContract $cartItemQuery
    */
    public function __construct(
        private CartItemQueryContract $cartItemQuery,
    ) {}

    /**
     * @param Cart $cart
     * @param ProductVariant $variant
     *
     * @return bool
    */
    public function isAvailable(Cart $cart, ProductVariant $variant): bool
    {
        $existingQuantity = $this->cartItemQuery->getExistingQuantity($cart, $variant);
        $availableStock = $variant->getStock()?->getQuantityAvailable() ?? 0;
        $availableEans = $this->cartItemQuery->getAvailableEansCount($variant);

        $maxAvailable = min($availableStock, $availableEans);

        return $maxAvailable > $existingQuantity;
    }
}
