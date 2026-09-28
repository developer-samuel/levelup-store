<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Stripe;

final readonly class StripeLineItemPriceObject
{
    /**
     * @param string $currency
     * @param string $productName
     * @param int $unitAmount
     */
    public function __construct(
        public string $currency,
        public string $productName,
        public int $unitAmount,
    ) {}
}
