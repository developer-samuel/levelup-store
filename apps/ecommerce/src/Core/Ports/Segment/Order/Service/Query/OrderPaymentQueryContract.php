<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject
};

interface OrderPaymentQueryContract
{
    /** @param StripeLineItemObject[] $lineItems */
    public function initiateCardPayment(array $lineItems, OrderCreatePayload $payload): string;

    public function extractPayloadFromMetadata(StripeCheckoutObject $session): OrderCreatePayload;
    public function shouldProcessPayment(int $userId, OrderCreatePayload $payload): bool;
    public function retrieveCheckoutSession(string $sessionId): StripeCheckoutObject;
}
