<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Policy;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Product\Entity\Variant\ProductVariant
};

interface CartItemAvailabilityPolicyContract
{
    public function isAvailable(Cart $cart, ProductVariant $variant): bool;
}
