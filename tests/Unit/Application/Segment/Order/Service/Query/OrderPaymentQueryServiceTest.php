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
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Order\Service\Query\OrderPaymentQueryContract
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

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderPaymentQueryContract::class, $this->service);
    }

    public function testInitiateCardPaymentReturnsPaymentUrl(): void
    {
        $this->stripePaymentAdapter
            ->method('initiateCheckout')
            ->willReturn('https://stripe.com/pay/abc');

        $result = $this->service->initiateCardPayment([], $this->buildPayload());

        $this->assertSame('https://stripe.com/pay/abc', $result);
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
            ->expects($this->once())
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

    public function testExtractPayloadFromMetadataReturnsOrderCreatePayload(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        $this->assertInstanceOf(OrderCreatePayload::class, $result);
    }

    public function testExtractPayloadFromMetadataBuildsPersonalData(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        $this->assertSame('test@example.com', $result->personal->email);
        $this->assertSame('Test', $result->personal->firstName);
        $this->assertSame('User', $result->personal->lastName);
    }

    public function testExtractPayloadFromMetadataBuildsBillingData(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession(overrides: [
            'billing_country' => '3',
        ]));

        $this->assertSame(3, $result->billing->country);
        $this->assertSame('Centrum', $result->billing->street);
        $this->assertSame('12345', $result->billing->postalCode);
        $this->assertSame('Bratislava', $result->billing->city);
    }

    public function testExtractPayloadFromMetadataSetsCardPaymentMethod(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        $this->assertSame(OrderPaymentMethod::CARD, $result->paymentMethod);
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

        $this->assertTrue($result->sendShipping);
        $this->assertNotNull($result->shipping);
        $this->assertSame(2, $result->shipping->country);
        $this->assertSame('Antona Bernoláka', $result->shipping->street);
    }

    public function testExtractPayloadFromMetadataReturnsNullShippingWhenSendShippingFalse(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession());

        $this->assertFalse($result->sendShipping);
        $this->assertNull($result->shipping);
    }

    public function testExtractPayloadFromMetadataReturnsNullShippingWhenCountryMissing(): void
    {
        $result = $this->service->extractPayloadFromMetadata($this->buildSession(overrides: [
            'send_shipping' => '1',
        ]));

        $this->assertNull($result->shipping);
    }

    public function testShouldProcessPaymentReturnsTrueWhenCartExistsAndCardPayment(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn($this->createMock(Cart::class));

        $result = $this->service->shouldProcessPayment(1, $this->buildPayload(OrderPaymentMethod::CARD));

        $this->assertTrue($result);
    }

    public function testShouldProcessPaymentReturnsFalseWhenCartNotFound(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $result = $this->service->shouldProcessPayment(1, $this->buildPayload(OrderPaymentMethod::CARD));

        $this->assertFalse($result);
    }

    public function testShouldProcessPaymentReturnsFalseForCashPayment(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn($this->createMock(Cart::class));

        $result = $this->service->shouldProcessPayment(1, $this->buildPayload(OrderPaymentMethod::CASH));

        $this->assertFalse($result);
    }

    public function testRetrieveCheckoutSessionDelegatesToStripeAdapter(): void
    {
        $session = new StripeCheckoutObject(metadata: ['x' => 'y'], amountTotal: 1000, paymentIntent: 'pi_abc');

        $this->stripePaymentAdapter
            ->expects($this->once())
            ->method('retrieveCheckoutSession')
            ->with('sess_abc')
            ->willReturn($session);

        $result = $this->service->retrieveCheckoutSession('sess_abc');

        $this->assertSame($session, $result);
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
