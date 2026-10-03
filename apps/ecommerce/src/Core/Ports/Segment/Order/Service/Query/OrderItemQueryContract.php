<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject
};

interface OrderItemQueryContract
{
    /**
     * @param CartItem[] $items
     *
     * @return StripeLineItemObject[]
    */
    public function prepareLineItems(array $items): array;

    public function isStockAvailable(?ProductVariantStock $stock): bool;
}
