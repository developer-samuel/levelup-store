<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Stripe;

final readonly class StripeLineItemObject
{
    public function __construct(
        public StripeLineItemPriceObject $price,
        public int $quantity,
    ) {}
}
