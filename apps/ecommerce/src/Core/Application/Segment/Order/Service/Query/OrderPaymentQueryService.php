<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject
};

use App\Core\Ports\{
    Gateways\External\Payment\Stripe\StripePaymentGatewayContract,
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Order\Service\Query\OrderPaymentQueryContract
};

final readonly class OrderPaymentQueryService implements OrderPaymentQueryContract
{
    /**
     * @param CartRepositoryContract $cartRepository
     * @param StripePaymentGatewayContract $stripePaymentAdapter
    */
    public function __construct(
        private CartRepositoryContract $cartRepository,
        private StripePaymentGatewayContract $stripePaymentAdapter,
    ) {}

    /**
     * @param StripeLineItemObject[] $lineItems
     * @param OrderCreatePayload $payload
     *
     * @return string
    */
    public function initiateCardPayment(array $lineItems, OrderCreatePayload $payload): string
    {
        return $this->stripePaymentAdapter->initiateCheckout($lineItems, $payload);
    }

    /**
     * @param StripeCheckoutObject $session
     *
     * @return OrderCreatePayload
     *
     * @throws \InvalidArgumentException
    */
    public function extractPayloadFromMetadata(StripeCheckoutObject $session): OrderCreatePayload
    {
        $meta = $session->metadata;
        if ($meta === []) {
            throw new \InvalidArgumentException('Stripe session metadata is missing or invalid.');
        }

        $sendShipping = ($meta['send_shipping'] ?? '0') === '1';

        return new OrderCreatePayload(
            personal: $this->buildPersonal($meta),
            sendShipping: $sendShipping,
            paymentMethod: OrderPaymentMethod::CARD,
            billing: $this->buildBilling($meta),
            shipping: $this->buildShipping($meta, $sendShipping),
        );
    }

    /**
     * @param array<string, string> $meta
     *
     * @return OrderPersonalObject
    */
    private function buildPersonal(array $meta): OrderPersonalObject
    {
        return new OrderPersonalObject(
            email: $meta['personal_email'] ?? '',
            firstName: $meta['personal_first_name'] ?? '',
            lastName: $meta['personal_last_name'] ?? '',
        );
    }

    /**
     * @param array<string, string> $meta
     *
     * @return OrderBillingObject
    */
    private function buildBilling(array $meta): OrderBillingObject
    {
        return new OrderBillingObject(
            country: (int) ($meta['billing_country'] ?? 0),
            street: $meta['billing_street'] ?? '',
            postalCode: $meta['billing_postal'] ?? '',
            city: $meta['billing_city'] ?? '',
        );
    }

    /**
     * @param array<string, string> $meta
     * @param bool $sendShipping
     *
     * @return OrderShippingObject|null
    */
    private function buildShipping(array $meta, bool $sendShipping): ?OrderShippingObject
    {
        if (!$sendShipping || !isset($meta['shipping_country'])) {
            return null;
        }

        return new OrderShippingObject(
            country: (int) $meta['shipping_country'],
            street: $meta['shipping_street'] ?? '',
            postalCode: $meta['shipping_postal'] ?? '',
            city: $meta['shipping_city'] ?? '',
        );
    }

    /**
     * @param int $userId
     * @param OrderCreatePayload $payload
     *
     * @return bool
    */
    public function shouldProcessPayment(int $userId, OrderCreatePayload $payload): bool
    {
        $cart = $this->cartRepository->findCartForUser($userId);

        return $cart !== null && $payload->paymentMethod === OrderPaymentMethod::CARD;
    }

    /**
     * @param string $sessionId
     *
     * @return StripeCheckoutObject
    */
    public function retrieveCheckoutSession(string $sessionId): StripeCheckoutObject
    {
        return $this->stripePaymentAdapter->retrieveCheckoutSession($sessionId);
    }
}
