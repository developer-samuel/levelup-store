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
    public function __construct(
        private CartItemQueryContract $cartItemQuery,
    ) {}

    public function isAvailable(Cart $cart, ProductVariant $variant): bool
    {
        $existingQuantity = $this->cartItemQuery->getExistingQuantity($cart, $variant);
        $availableStock = $variant->getStock()?->getQuantityAvailable() ?? 0;
        $availableEans = $this->cartItemQuery->getAvailableEansCount($variant);

        $maxAvailable = min($availableStock, $availableEans);

        return $maxAvailable > $existingQuantity;
    }
}
