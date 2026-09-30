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
    /**
     * @param StripeLineItemObject[] $lineItems
     * @param OrderCreatePayload $payload
     *
     * @return string
    */
    public function initiateCardPayment(array $lineItems, OrderCreatePayload $payload): string;

    /**
     * @param StripeCheckoutObject $session
     *
     * @return OrderCreatePayload
    */
    public function extractPayloadFromMetadata(StripeCheckoutObject $session): OrderCreatePayload;

    /**
     * @param int $userId
     * @param OrderCreatePayload $payload
     *
     * @return bool
    */
    public function shouldProcessPayment(int $userId, OrderCreatePayload $payload): bool;

    /**
     * @param string $sessionId
     *
     * @return StripeCheckoutObject
    */
    public function retrieveCheckoutSession(string $sessionId): StripeCheckoutObject;
}
