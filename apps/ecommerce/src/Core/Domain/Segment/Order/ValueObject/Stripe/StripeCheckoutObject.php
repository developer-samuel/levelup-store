<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Stripe;

final readonly class StripeCheckoutObject
{
    /** @param array<string, string> $metadata */
    public function __construct(
        public array $metadata,
        public int $amountTotal,
        public ?string $paymentIntent,
    ) {}
}
