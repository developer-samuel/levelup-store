<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Payment\Stripe;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Stripe\{
    ApiRequestor,
    Checkout\Session as StripeSession,
    HttpClient\ClientInterface,
    Stripe,
    StripeObject
};

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\Address\OrderShippingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemPriceObject
};

use App\Core\Ports\Payment\Stripe\StripeSdkContract;

use App\Adapters\External\Payment\Stripe\StripePaymentAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Payment\Stripe\StripePaymentAdapter
 *
 * @phpstan-import-type LineItem from StripePaymentAdapter
*/
final class StripePaymentAdapterTest extends TestCase
{
    private UrlGeneratorInterface&MockObject $urlGenerator;
    private StripeSdkContract&MockObject $stripeSdk;
    private StripePaymentAdapter $adapter;

    protected function setUp(): void
    {
        Stripe::setApiKey('sk_test_fake');

        $this->initMocks();
        $this->initAdapter();
    }

    public function testInitiateCheckoutReturnsUrl(): void
    {
        $this->stripeSdk->expects(self::once())->method('initialize');

        $this->urlGenerator
            ->method('generate')
            ->willReturnMap([
                ['orders_success', [], UrlGeneratorInterface::ABSOLUTE_URL, 'https://example.com/orders/success'],
                ['orders_cancel',  [], UrlGeneratorInterface::ABSOLUTE_URL, 'https://example.com/orders/cancel'],
            ]);

        ApiRequestor::setHttpClient($this->buildStripeHttpMock([
            'id'     => 'sk_test_123',
            'object' => 'checkout.session',
            'url'    => 'https://checkout.stripe.com/pay/sk_test_123',
        ]));

        $lineItems = [new StripeLineItemObject(new StripeLineItemPriceObject('eur', 'Product', 1000), 1)];
        $payload = $this->buildPayload(sendShipping: false, shipping: null);

        $result = $this->adapter->initiateCheckout($lineItems, $payload);

        self::assertSame('https://checkout.stripe.com/pay/sk_test_123', $result);
    }

    public function testRetrieveCheckoutSessionReturnsObject(): void
    {
        $this->stripeSdk->expects(self::once())->method('initialize');

        ApiRequestor::setHttpClient($this->buildStripeHttpMock([
            'id'             => 'sk_test_123',
            'object'         => 'checkout.session',
            'amount_total'   => 2000,
            'payment_intent' => 'pi_abc123',
            'metadata'       => ['personal_email' => 'test@example.com'],
        ]));

        $result = $this->adapter->retrieveCheckoutSession('sk_test_123');

        self::assertSame(2000, $result->amountTotal);
        self::assertSame('pi_abc123', $result->paymentIntent);
        self::assertSame('test@example.com', $result->metadata['personal_email']);
    }

    public function testGenerateUrlCallsUrlGenerator(): void
    {
        $this->urlGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('orders_success', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/orders/success');

        $result = $this->callPrivate('generateUrl', ['orders_success']);

        self::assertSame('https://example.com/orders/success', $result);
    }

    public function testMapLineItemsMapsCorrectly(): void
    {
        $lineItems = [
            new StripeLineItemObject(
                new StripeLineItemPriceObject('eur', 'Product A', 1999),
                2,
            ),
        ];

        /** @var array<int, LineItem> $result */
        $result = $this->callPrivate('mapLineItems', [$lineItems]);

        self::assertCount(1, $result);
        self::assertSame('eur', $result[0]['price_data']['currency']);
        self::assertSame('Product A', $result[0]['price_data']['product_data']['name']);
        self::assertSame(1999, $result[0]['price_data']['unit_amount']);
        self::assertSame(2, $result[0]['quantity']);
    }

    public function testMapLineItemsReturnsEmptyArray(): void
    {
        /** @var array<int, mixed> $result */
        $result = $this->callPrivate('mapLineItems', [[]]);

        self::assertIsArray($result);
        self::assertEmpty($result);
    }

    public function testBuildMetadataWithoutShipping(): void
    {
        $payload = $this->buildPayload(sendShipping: false, shipping: null);

        /** @var array<string, string> $result */
        $result = $this->callPrivate('buildMetadata', [$payload]);

        self::assertSame('test@example.com', $result['personal_email']);
        self::assertSame('John', $result['personal_first_name']);
        self::assertSame('Doe', $result['personal_last_name']);
        self::assertSame('1', $result['billing_country']);
        self::assertSame('Main Street', $result['billing_street']);
        self::assertSame('12345', $result['billing_postal']);
        self::assertSame('Bratislava', $result['billing_city']);
        self::assertSame('0', $result['send_shipping']);
        self::assertArrayNotHasKey('shipping_country', $result);
    }

    public function testBuildMetadataWithShipping(): void
    {
        $shipping = new OrderShippingObject(2, 'Side Street', '54321', 'Košice');
        $payload = $this->buildPayload(sendShipping: true, shipping: $shipping);

        /** @var array<string, string> $result */
        $result = $this->callPrivate('buildMetadata', [$payload]);

        self::assertSame('1', $result['send_shipping']);
        self::assertSame('2', $result['shipping_country']);
        self::assertSame('Side Street', $result['shipping_street']);
        self::assertSame('54321', $result['shipping_postal']);
        self::assertSame('Košice', $result['shipping_city']);
    }

    public function testExtractCheckoutUrlReturnsUrl(): void
    {
        $session = StripeSession::constructFrom(['url' => 'https://checkout.stripe.com/pay/session_123']);

        $result = $this->callPrivate('extractCheckoutUrl', [$session]);

        self::assertSame('https://checkout.stripe.com/pay/session_123', $result);
    }

    public function testExtractCheckoutUrlThrowsOnEmptyUrl(): void
    {
        $session = StripeSession::constructFrom(['url' => '']);

        $this->expectException(\Exception::class);

        $this->callPrivate('extractCheckoutUrl', [$session]);
    }

    public function testExtractCheckoutUrlThrowsOnMissingUrl(): void
    {
        $session = StripeSession::constructFrom(['url' => null]);

        $this->expectException(\Exception::class);

        $this->callPrivate('extractCheckoutUrl', [$session]);
    }

    public function testExtractMetadataReturnsArray(): void
    {
        $session = StripeSession::constructFrom([
            'metadata' => ['personal_email' => 'test@example.com'],
        ]);

        $result = $this->callPrivate('extractMetadata', [$session]);

        self::assertSame(['personal_email' => 'test@example.com'], $result);
    }

    public function testExtractMetadataConvertsStripeObject(): void
    {
        $stripeObject = StripeObject::constructFrom(['personal_email' => 'test@example.com']);

        $session = StripeSession::constructFrom([]);
        $session->metadata = $stripeObject;

        $result = $this->callPrivate('extractMetadata', [$session]);

        self::assertIsArray($result);
        self::assertSame('test@example.com', $result['personal_email']);
    }

    public function testExtractMetadataThrowsOnMissingMetadata(): void
    {
        $session = StripeSession::constructFrom(['metadata' => null]);

        $this->expectException(\InvalidArgumentException::class);

        $this->callPrivate('extractMetadata', [$session]);
    }

    public function testExtractMetadataThrowsOnEmptyMetadata(): void
    {
        $session = StripeSession::constructFrom(['metadata' => []]);

        $this->expectException(\InvalidArgumentException::class);

        $this->callPrivate('extractMetadata', [$session]);
    }

    public function testExtractPaymentIntentReturnsStringWhenPresent(): void
    {
        $session = StripeSession::constructFrom(['payment_intent' => 'pi_abc123']);

        $result = $this->callPrivate('extractPaymentIntent', [$session]);

        self::assertSame('pi_abc123', $result);
    }

    public function testExtractPaymentIntentReturnsNullWhenNotString(): void
    {
        $session = StripeSession::constructFrom(['payment_intent' => null]);

        $result = $this->callPrivate('extractPaymentIntent', [$session]);

        self::assertNull($result);
    }

    public function testRetrieveCheckoutSessionReturnsZeroAmountWhenNull(): void
    {
        $this->stripeSdk->expects(self::once())->method('initialize');

        ApiRequestor::setHttpClient($this->buildStripeHttpMock([
            'id'             => 'sk_test_123',
            'object'         => 'checkout.session',
            'amount_total'   => null,
            'payment_intent' => null,
            'metadata'       => ['personal_email' => 'test@example.com'],
        ]));

        $result = $this->adapter->retrieveCheckoutSession('sk_test_123');

        self::assertSame(0, $result->amountTotal);
    }

    public function testMapLineItemsReturnsAllItemsWhenMultiple(): void
    {
        $lineItems = [
            new StripeLineItemObject(new StripeLineItemPriceObject('eur', 'Product A', 1000), 1),
            new StripeLineItemObject(new StripeLineItemPriceObject('eur', 'Product B', 2000), 2),
        ];

        /** @var array<int, LineItem> $result */
        $result = $this->callPrivate('mapLineItems', [$lineItems]);

        self::assertCount(2, $result);
        self::assertSame('Product A', $result[0]['price_data']['product_data']['name']);
        self::assertSame('Product B', $result[1]['price_data']['product_data']['name']);
    }

    public function testMapLineItemsWithNonSequentialKeys(): void
    {
        $lineItems = [
            5  => new StripeLineItemObject(new StripeLineItemPriceObject('eur', 'Product A', 1000), 1),
            10 => new StripeLineItemObject(new StripeLineItemPriceObject('eur', 'Product B', 2000), 1),
        ];

        /** @var array<int, LineItem> $result */
        $result = $this->callPrivate('mapLineItems', [$lineItems]);

        self::assertCount(2, $result);
    }

    public function testBuildSessionParamsStructure(): void
    {
        $this->urlGenerator
            ->method('generate')
            ->willReturnMap([
                ['orders_success', [], UrlGeneratorInterface::ABSOLUTE_URL, 'https://example.com/orders/success'],
                ['orders_cancel',  [], UrlGeneratorInterface::ABSOLUTE_URL, 'https://example.com/orders/cancel'],
            ]);

        $lineItems = [new StripeLineItemObject(new StripeLineItemPriceObject('eur', 'Product', 1000), 1)];
        $payload = $this->buildPayload(sendShipping: false, shipping: null);

        /** @var array<string, mixed> $result */
        $result = $this->callPrivate('buildSessionParams', [$lineItems, $payload]);

        self::assertSame(['card'], $result['payment_method_types']);
        self::assertSame('payment', $result['mode']);
        self::assertSame('https://example.com/orders/success?session_id={CHECKOUT_SESSION_ID}', $result['success_url']);
        self::assertSame('https://example.com/orders/cancel', $result['cancel_url']);
        self::assertArrayHasKey('line_items', $result);
        self::assertArrayHasKey('metadata', $result);
    }

    public function testExtractMetadataReturnsAllKeys(): void
    {
        $session = StripeSession::constructFrom([
            'metadata' => [
                'personal_email'      => 'test@example.com',
                'personal_first_name' => 'John',
                'billing_country'     => '1',
            ],
        ]);

        /** @var array<string, string> $result */
        $result = $this->callPrivate('extractMetadata', [$session]);

        self::assertCount(3, $result);
        self::assertArrayHasKey('personal_email', $result);
        self::assertArrayHasKey('personal_first_name', $result);
        self::assertArrayHasKey('billing_country', $result);
    }

    private function initMocks(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->stripeSdk = $this->createMock(StripeSdkContract::class);
    }

    private function initAdapter(): void
    {
        $this->adapter = new StripePaymentAdapter(
            $this->urlGenerator,
            $this->stripeSdk,
        );
    }

    private function buildPayload(bool $sendShipping, ?OrderShippingObject $shipping): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('test@example.com', 'John', 'Doe'),
            sendShipping:  $sendShipping,
            paymentMethod: OrderPaymentMethod::CARD,
            billing:       new OrderBillingObject(1, 'Main Street', '12345', 'Bratislava'),
            shipping:      $shipping,
        );
    }

    /**
     * @param array<int, mixed> $args
    */
    private function callPrivate(string $method, array $args = []): mixed
    {
        $reflection = new \ReflectionMethod($this->adapter, $method);

        return $reflection->invokeArgs($this->adapter, $args);
    }

    /**
     * @param array<string, mixed> $responseData
    */
    private function buildStripeHttpMock(array $responseData): ClientInterface
    {
        return new class($responseData) implements ClientInterface {
            /** @param array<string, mixed> $responseData */
            public function __construct(private array $responseData) {}

            /**
             * @param array<array-key, mixed> $headers
             * @param array<array-key, mixed> $params
             *
             * @return array{0: string, 1: int, 2: string[]}
            */
            public function request(
                $method,
                $absUrl,
                $headers,
                $params,
                $hasFile,
                $apiMode = 'v1',
                $maxNetworkRetries = null,
            ): array {
                return [(string) json_encode($this->responseData), 200, []];
            }
        };
    }
}
