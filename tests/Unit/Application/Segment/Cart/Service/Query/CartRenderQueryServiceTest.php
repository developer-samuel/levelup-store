<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Cart\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\ValueObject\CartItemObject,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Cart\Service\Query\CartRenderQueryService;

use App\Core\Ports\{
    Segment\Cart\Renderer\CartRendererContract,
    Segment\Cart\Service\Query\CartPriceQueryContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Segment\Cart\Service\Query\CartSummaryQueryContract
};

use Tests\Support\Stub\UserStub;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Query\CartRenderQueryService
*/
class CartRenderQueryServiceTest extends TestCase
{
    use UserStub;

    private CartSummaryQueryContract&MockObject $cartSummaryQuery;
    private CartPriceQueryContract&MockObject $cartPriceQuery;
    private CartRendererContract&MockObject $cartRenderer;
    private CartRenderQueryService $service;
    private User $user;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createUserWithId(1);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartRenderQueryContract::class, $this->service);
    }

    public function testBuildCartResponseReturnsSuccessResponse(): void
    {
        $summary = ['totalItems' => 0, 'totalPrice' => '0 €'];

        $this->cartSummaryQuery->method('findCartItemsForUser')->with(1)->willReturn([]);
        $this->cartPriceQuery->method('calculateTotalPrice')->willReturn(0.0);
        $this->cartRenderer->method('renderCart')->willReturn('<div></div>');
        $this->cartSummaryQuery
            ->method('buildSuccessResponse')
            ->with('Item added.', '<div></div>', $summary)
            ->willReturn([
                'html'       => '<div></div>',
                'totalItems' => 0,
                'totalPrice' => '0 €',
                'message'    => 'Item added.',
                'success'    => true,
                'status'     => null,
            ]);

        $result = $this->service->buildCartResponse($this->user, 'Item added.');

        $this->assertSame('Item added.', $result['message']);
        $this->assertTrue($result['success']);
    }

    public function testBuildCartResponseReturnsErrorResponseWhenIsErrorTrue(): void
    {
        $summary = ['totalItems' => 0, 'totalPrice' => '0 €'];

        $this->cartSummaryQuery->method('findCartItemsForUser')->willReturn([]);
        $this->cartPriceQuery->method('calculateTotalPrice')->willReturn(0.0);
        $this->cartRenderer->method('renderCart')->willReturn('<div></div>');
        $this->cartSummaryQuery
            ->method('buildErrorResponse')
            ->with('Error.', '<div></div>', $summary, 422)
            ->willReturn([
                'html'       => '<div></div>',
                'totalItems' => 0,
                'totalPrice' => '0 €',
                'message'    => 'Error.',
                'success'    => false,
                'status'     => 422,
            ]);

        $result = $this->service->buildCartResponse($this->user, 'Error.', true);

        $this->assertFalse($result['success']);
        $this->assertSame(422, $result['status']);
    }

    public function testBuildCartResponseCallsRenderCartWithTransformedItems(): void
    {
        $variant = $this->createMock(ProductVariant::class);
        $item = new CartItemObject(1, 1, $variant, '10,00 €', false, 0.0);

        $this->cartSummaryQuery->method('findCartItemsForUser')->willReturn([$item]);
        $this->cartPriceQuery->method('calculateTotalPrice')->willReturn(10.0);
        $this->cartSummaryQuery
            ->method('buildSuccessResponse')
            ->willReturn([
                'html'       => '<div>item</div>',
                'totalItems' => 1,
                'totalPrice' => '10,00 €',
                'message'    => 'ok',
                'success'    => true,
                'status'     => null,
            ]);

        $this->cartRenderer
            ->expects($this->once())
            ->method('renderCart')
            ->with($this->isType('array'))
            ->willReturn('<div>item</div>');

        $this->service->buildCartResponse($this->user, 'ok');
    }

    public function testBuildCartResponseCallsCalculateTotalPrice(): void
    {
        $this->cartSummaryQuery->method('findCartItemsForUser')->willReturn([]);
        $this->cartRenderer->method('renderCart')->willReturn('');
        $this->cartSummaryQuery
            ->method('buildSuccessResponse')
            ->willReturn([
                'html'       => '',
                'totalItems' => 0,
                'totalPrice' => '0 €',
                'message'    => 'ok',
                'success'    => true,
                'status'     => null,
            ]);

        $this->cartPriceQuery
            ->expects($this->once())
            ->method('calculateTotalPrice')
            ->willReturn(0.0);

        $this->service->buildCartResponse($this->user, 'ok');
    }

    private function initMocks(): void
    {
        $this->cartSummaryQuery = $this->createMock(CartSummaryQueryContract::class);
        $this->cartPriceQuery = $this->createMock(CartPriceQueryContract::class);
        $this->cartRenderer = $this->createMock(CartRendererContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartRenderQueryService(
            $this->cartSummaryQuery,
            $this->cartPriceQuery,
            $this->cartRenderer,
        );
    }
}
