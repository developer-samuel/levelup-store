<?php

declare(strict_types=1);

namespace App\Adapters\External\Payment\Stripe;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Stripe\{
    Checkout\Session as StripeSession,
    StripeObject
};

use App\Core\Domain\{
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject
};

use App\Core\Ports\{
    Gateways\External\Payment\Stripe\StripePaymentGatewayContract,
    Payment\Stripe\StripeSdkContract
};

/**
 * @phpstan-type StripePriceData array{
 *     currency: string,
 *     product_data: array{name: string},
 *     unit_amount: int
 * }
 * @phpstan-type LineItem array{
 *     price_data: StripePriceData,
 *     quantity: int
 * }
*/
final class StripePaymentAdapter implements StripePaymentGatewayContract
{
    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param StripeSdkContract $stripeSdk
    */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private StripeSdkContract $stripeSdk,
    ) {}

    /**
     * @param StripeLineItemObject[] $lineItems
     * @param OrderCreatePayload $payload
     *
     * @return string
    */
    public function initiateCheckout(array $lineItems, OrderCreatePayload $payload): string
    {
        $this->stripeSdk->initialize();

        $checkoutSession = StripeSession::create(
            $this->buildSessionParams($lineItems, $payload),
        );

        return $this->extractCheckoutUrl($checkoutSession);
    }

    /**
     * @param string $sessionId
     *
     * @return StripeCheckoutObject
    */
    public function retrieveCheckoutSession(string $sessionId): StripeCheckoutObject
    {
        $this->stripeSdk->initialize();

        $session = StripeSession::retrieve($sessionId);
        $metadata = $this->extractMetadata($session);

        return new StripeCheckoutObject(
            metadata:      $metadata,
            amountTotal:   $session->amount_total ?? 0,
            paymentIntent: $this->extractPaymentIntent($session),
        );
    }

    /**
     * @param StripeLineItemObject[] $lineItems
     * @param OrderCreatePayload $payload
     *
     * @return array{
     *     payment_method_types: list<string>,
     *     line_items: array<int, LineItem>,
     *     mode: string,
     *     success_url: string,
     *     cancel_url: string,
     *     metadata: array<string, string>
     * }
    */
    private function buildSessionParams(array $lineItems, OrderCreatePayload $payload): array
    {
        return [
            'payment_method_types' => ['card'],
            'line_items'           => $this->mapLineItems($lineItems),
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('orders_success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => $this->generateUrl('orders_cancel'),
            'metadata'             => $this->buildMetadata($payload),
        ];
    }

    /**
     * @param StripeLineItemObject[] $lineItems
     *
     * @return array<int, LineItem>
    */
    private function mapLineItems(array $lineItems): array
    {
        $result = [];

        foreach ($lineItems as $item) {
            $result[] = [
                'price_data' => [
                    'currency'     => $item->price->currency,
                    'product_data' => [
                        'name' => $item->price->productName,
                    ],
                    'unit_amount' => $item->price->unitAmount,
                ],
                'quantity' => $item->quantity,
            ];
        }

        return $result;
    }

    /**
     * @param string $route
     *
     * @return string
    */
    private function generateUrl(string $route): string
    {
        return $this->urlGenerator->generate(
            $route,
            [],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    /**
     * @param OrderCreatePayload $payload
     *
     * @return array<string, string>
    */
    private function buildMetadata(OrderCreatePayload $payload): array
    {
        $meta = [
            'personal_email'       => $payload->personal->email,
            'personal_first_name'  => $payload->personal->firstName,
            'personal_last_name'   => $payload->personal->lastName,
            'billing_country'      => (string) $payload->billing->country,
            'billing_street'       => $payload->billing->street,
            'billing_postal'       => $payload->billing->postalCode,
            'billing_city'         => $payload->billing->city,
            'send_shipping'        => $payload->sendShipping ? '1' : '0',
        ];

        if ($payload->shipping !== null) {
            $meta['shipping_country'] = (string) $payload->shipping->country;
            $meta['shipping_street'] = $payload->shipping->street;
            $meta['shipping_postal'] = $payload->shipping->postalCode;
            $meta['shipping_city'] = $payload->shipping->city;
        }

        return $meta;
    }

    /**
     * @param StripeSession $session
     *
     * @return string
     *
     * @throws \Exception
    */
    private function extractCheckoutUrl(StripeSession $session): string
    {
        $url = $session->url;
        if (!is_string($url) || $url === '') {
            throw new \Exception('Stripe checkout session URL is missing or invalid.');
        }

        return $url;
    }

    /**
     * @param StripeSession $session
     *
     * @return array<string, string>
     *
     * @throws \InvalidArgumentException
    */
    private function extractMetadata(StripeSession $session): array
    {
        $metadata = $session->metadata;

        if ($metadata instanceof StripeObject) {
            $metadata = $metadata->toArray();
        }

        if (!is_array($metadata) || $metadata === []) {
            throw new \InvalidArgumentException('Stripe session metadata is missing or invalid.');
        }

        /** @var array<string, string> $metadata */
        return $metadata;
    }

    /**
     * @param StripeSession $session
     *
     * @return string|null
    */
    private function extractPaymentIntent(StripeSession $session): ?string
    {
        return is_string($session->payment_intent) ? $session->payment_intent : null;
    }
}
