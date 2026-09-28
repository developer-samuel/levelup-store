<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Order\Enum\OrderPaymentMethod,
    Segment\Order\Payload\OrderCreatePayload,
    Segment\Order\ValueObject\Address\OrderBillingObject,
    Segment\Order\ValueObject\OrderPersonalObject,
    Segment\Order\ValueObject\Stripe\StripeCheckoutObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject,
    Segment\Order\ValueObject\Stripe\StripeLineItemPriceObject
};

use App\Core\Application\Segment\Order\Service\Query\OrderPaymentQueryService;

use App\Core\Ports\{
    Gateways\External\Payment\Stripe\StripePaymentGatewayContract,
    Segment\Cart\Repository\CartRepositoryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Query\OrderPaymentQueryService
*/
final class OrderPaymentQueryServiceTest extends TestCase
{
    private CartRepositoryContract&MockObject $cartRepository;
    private StripePaymentGatewayContract&MockObject $stripePaymentAdapter;
    private OrderPaymentQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testInitiateCardPaymentReturnsPaymentUrl(): void
    {
        $this->stripePaymentAdapter
            ->method('initiateCheckout')
            ->willReturn('https://stripe.com/pay/abc');

        $result = $this->service->initiateCardPayment([], $this->buildPayload());

        self::assertSame('https://stripe.com/pay/abc', $result);
    }

    public function testInitiateCardPaymentDelegatesToStripeAdapter(): void
    {
        $lineItems = [
            new StripeLineItemObject(
                price: new StripeLineItemPriceObject('eur', 'Test Product', 9999),
                quantity: 1,
            ),
        ];
        $payload = $this->buildPayload();

        $this->stripePaymentAdapter
            ->expects(self::once())
            ->method('initiateCheckout')
            ->with($lineItems, $payload)
            ->willReturn('https://stripe.com/pay/abc');

        $this->service->initiateCardPayment($lineItems, $payload);
    }

    public function testExtractPayloadFromMetadataThrowsWhenMetadataIsEmpty(): void
    {
        $session = new StripeCheckoutObject(metadata: [], amountTotal: 1000, paymentIntent: 'pi_abc');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/metadata/i');

        $this->service->extractPayloadFromMetadata($session);
    }

    public function testExtractPayloadFromMetadataBuildsPersonalData(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        self::assertSame('test@example.com', $result->personal->email);
        self::assertSame('Test', $result->personal->firstName);
        self::assertSame('User', $result->personal->lastName);
    }

    public function testExtractPayloadFromMetadataBuildsBillingData(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession(overrides: [
            'billing_country' => '3',
        ]));

        self::assertSame(3, $result->billing->country);
        self::assertSame('Centrum', $result->billing->street);
        self::assertSame('12345', $result->billing->postalCode);
        self::assertSame('Bratislava', $result->billing->city);
    }

    public function testExtractPayloadFromMetadataSetsCardPaymentMethod(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        self::assertSame(OrderPaymentMethod::CARD, $result->paymentMethod);
    }

    public function testExtractPayloadFromMetadataBuildsShippingWhenSendShippingTrue(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession(overrides: [
            'send_shipping'    => '1',
            'shipping_country' => '2',
            'shipping_street'  => 'Antona Bernoláka',
            'shipping_postal'  => '99999',
            'shipping_city'    => 'Žilina',
        ]));

        self::assertTrue($result->sendShipping);
        self::assertNotNull($result->shipping);
        self::assertSame(2, $result->shipping->country);
        self::assertSame('Antona Bernoláka', $result->shipping->street);
    }

    public function testExtractPayloadFromMetadataReturnsNullShippingWhenSendShippingFalse(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        self::assertFalse($result->sendShipping);
        self::assertNull($result->shipping);
    }

    public function testExtractPayloadFromMetadataReturnsNullShippingWhenCountryMissing(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession(overrides: [
            'send_shipping' => '1',
        ]));

        self::assertNull($result->shipping);
    }

    public function testShouldProcessPaymentReturnsTrueWhenCartExistsAndCardPayment(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn($this->createMock(Cart::class));

        $result = $this->service->shouldProcessPayment(1, $this->buildPayload(OrderPaymentMethod::CARD));

        self::assertTrue($result);
    }

    public function testShouldProcessPaymentReturnsFalseWhenCartNotFound(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $result = $this->service->shouldProcessPayment(1, $this->buildPayload(OrderPaymentMethod::CARD));

        self::assertFalse($result);
    }

    public function testShouldProcessPaymentReturnsFalseForCashPayment(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn($this->createMock(Cart::class));

        $result = $this->service->shouldProcessPayment(1, $this->buildPayload(OrderPaymentMethod::CASH));

        self::assertFalse($result);
    }

    public function testRetrieveCheckoutSessionDelegatesToStripeAdapter(): void
    {
        $session = new StripeCheckoutObject(metadata: ['x' => 'y'], amountTotal: 1000, paymentIntent: 'pi_abc');

        $this->stripePaymentAdapter
            ->expects(self::once())
            ->method('retrieveCheckoutSession')
            ->with('sess_abc')
            ->willReturn($session);

        $result = $this->service->retrieveCheckoutSession('sess_abc');

        self::assertSame($session, $result);
    }

    private function initMocks(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryContract::class);
        $this->stripePaymentAdapter = $this->createMock(StripePaymentGatewayContract::class);
    }

    private function initService(): void
    {
        $this->service = new OrderPaymentQueryService(
            $this->cartRepository,
            $this->stripePaymentAdapter,
        );
    }

    private function buildPayload(OrderPaymentMethod $paymentMethod = OrderPaymentMethod::CARD): OrderCreatePayload
    {
        return new OrderCreatePayload(
            personal:      new OrderPersonalObject('t@e.com', 'T', 'U'),
            sendShipping:  false,
            paymentMethod: $paymentMethod,
            billing:       new OrderBillingObject(1, 'St', '123', 'City'),
        );
    }

    /**
     * @param array<string, string> $overrides
    */
    private function buildSession(array $overrides = []): StripeCheckoutObject
    {
        return new StripeCheckoutObject(
            metadata: array_merge([
                'personal_email'      => 'test@example.com',
                'personal_first_name' => 'Test',
                'personal_last_name'  => 'User',
                'billing_country'     => '1',
                'billing_street'      => 'Centrum',
                'billing_postal'      => '12345',
                'billing_city'        => 'Bratislava',
                'send_shipping'       => '0',
            ], $overrides),
            amountTotal:   1000,
            paymentIntent: 'pi_abc123',
        );
    }
}
