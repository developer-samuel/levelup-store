<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Stripe;

final readonly class StripeLineItemPriceObject
{
    public function __construct(
        public string $currency,
        public string $productName,
        public int $unitAmount,
    ) {}
}
