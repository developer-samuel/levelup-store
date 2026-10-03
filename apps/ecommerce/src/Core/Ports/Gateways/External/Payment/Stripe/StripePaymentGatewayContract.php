<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Payment\Stripe;

use App\Core\Domain\{
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject
};

interface StripePaymentGatewayContract
{
    /** @param StripeLineItemObject[] $lineItems */
    public function initiateCheckout(array $lineItems, OrderCreatePayload $payload): string;

    public function retrieveCheckoutSession(string $sessionId): StripeCheckoutObject;
}
