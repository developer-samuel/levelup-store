<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Stripe;

final readonly class StripeLineItemObject
{
    /**
     * @param StripeLineItemPriceObject $price
     * @param int $quantity
     */
    public function __construct(
        public StripeLineItemPriceObject $price,
        public int $quantity,
    ) {}
}
