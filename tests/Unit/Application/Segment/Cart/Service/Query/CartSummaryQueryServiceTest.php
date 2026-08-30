<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Cart\Service\Query;

use Doctrine\Common\Collections\ArrayCollection;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Cart\ValueObject\CartItemObject,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDiscount,
    Segment\Product\Entity\Variant\ProductVariantEan,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Product\Enum\ProductStockStatus,
    Segment\Product\Enum\Variant\ProductVariantEanStatus,
    Segment\Product\Enum\Variant\ProductVariantStatus
};

use App\Core\Application\Segment\Cart\Service\Query\CartSummaryQueryService;

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartSummaryQueryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Query\CartSummaryQueryService
*/
class CartSummaryQueryServiceTest extends TestCase
{
    private const EMPTY_SUMMARY = ['totalItems' => 0, 'totalPrice' => '0,00 €'];

    private CartRepositoryContract&MockObject $cartRepository;
    private ReviewQueryContract&MockObject $reviewQuery;
    private CartSummaryQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartSummaryQueryContract::class, $this->service);
    }

    public function testGetCartSummaryReturnsEmptyWhenCartNotFound(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $result = $this->service->getCartSummary(1);

        $this->assertSame([], $result['items']);
        $this->assertSame(0.0, $result['totalPrice']);
        $this->assertSame(0, $result['totalItems']);
    }

    public function testGetCartSummarySkipsItemsWithNoStock(): void
    {
        $this->assertCartSkipsVariant(null);
    }

    public function testGetCartSummarySkipsItemsWithZeroStock(): void
    {
        $this->assertCartSkipsVariant(0);
    }

    public function testGetCartSummaryIncludesItemsWithStock(): void
    {
        $variant = $this->createVariantWithStock(5);
        $variant->method('getId')->willReturn(1);
        $variant->method('getDiscountedPrice')->willReturn(49.99);
        $variant->method('getPrice')->willReturn(49.99);
        $variant->method('getDiscount')->willReturn(null);

        $item = $this->createItemWithVariant($variant);
        $item->method('getId')->willReturn(1);

        $cart = $this->createMock(Cart::class);
        $cart->method('getItems')->willReturn(new ArrayCollection([$item]));
        $cart->method('getId')->willReturn(1);

        $item->method('getCart')->willReturn($cart);

        $this->cartRepository->method('findCartForUser')->willReturn($cart);
        $this->reviewQuery->method('getAverageRatingByVariant')->willReturn(4.5);

        $result = $this->service->getCartSummary(1);

        $this->assertCount(1, $result['items']);
        $this->assertSame(49.99, $result['totalPrice']);
        $this->assertSame(1, $result['totalItems']);
    }

    public function testFindCartItemsForUserReturnsSummaryItems(): void
    {
        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $result = $this->service->findCartItemsForUser(1);

        $this->assertSame([], $result);
    }

    public function testBuildSuccessResponseReturnsCorrectStructure(): void
    {
        $summary = ['totalItems' => 2, 'totalPrice' => '99,98 €'];

        $result = $this->service->buildSuccessResponse('Added.', '<div>', $summary);

        $this->assertSame('<div>', $result['html']);
        $this->assertSame('Added.', $result['message']);
        $this->assertTrue($result['success']);
        $this->assertNull($result['status']);
        $this->assertSame(2, $result['totalItems']);
        $this->assertSame('99,98 €', $result['totalPrice']);
    }

    public function testBuildErrorResponseReturnsCorrectStructure(): void
    {
        $result = $this->service->buildErrorResponse('Error.', '<div>', self::EMPTY_SUMMARY, 422);

        $this->assertSame('<div>', $result['html']);
        $this->assertSame('Error.', $result['message']);
        $this->assertFalse($result['success']);
        $this->assertSame(422, $result['status']);
    }

    public function testBuildSuccessResponseSuccessIsTrue(): void
    {
        $result = $this->service->buildSuccessResponse('ok', '', self::EMPTY_SUMMARY);

        $this->assertTrue($result['success']);
    }

    public function testBuildErrorResponseSuccessIsFalse(): void
    {
        $result = $this->service->buildErrorResponse('fail', '', self::EMPTY_SUMMARY, 422);

        $this->assertFalse($result['success']);
    }

    public function testGetCartSummaryIncludesFormattedDiscountPriceWhenDiscountPresent(): void
    {
        $discount = $this->createMock(ProductVariantDiscount::class);

        $variant = $this->createVariantWithStock(5);
        $variant->method('getId')->willReturn(1);
        $variant->method('getDiscountedPrice')->willReturn(39.99);
        $variant->method('getPrice')->willReturn(49.99);
        $variant->method('getDiscount')->willReturn($discount);

        $item = $this->createItemWithVariant($variant);
        $item->method('getId')->willReturn(1);

        $cart = $this->createMock(Cart::class);
        $cart->method('getItems')->willReturn(new ArrayCollection([$item]));
        $cart->method('getId')->willReturn(1);
        $item->method('getCart')->willReturn($cart);

        $this->cartRepository->method('findCartForUser')->willReturn($cart);
        $this->reviewQuery->method('getAverageRatingByVariant')->willReturn(4.0);

        $result = $this->service->getCartSummary(1);

        $this->assertCount(1, $result['items']);

        /** @var CartItemObject $cartItemObject */
        $cartItemObject = $result['items'][0];

        $this->assertTrue($cartItemObject->hasDiscount);
        $this->assertNotNull($cartItemObject->formattedDiscountPrice);
    }

    private function initMocks(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryContract::class);
        $this->reviewQuery = $this->createMock(ReviewQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartSummaryQueryService(
            $this->cartRepository,
            $this->reviewQuery,
        );
    }

    private function assertCartSkipsVariant(?int $quantity): void
    {
        $variant = $this->createVariantWithStock($quantity);
        $cart = $this->createCartWithItems([$this->createItemWithVariant($variant)]);

        $this->cartRepository->method('findCartForUser')->willReturn($cart);

        $result = $this->service->getCartSummary(1);

        $this->assertSame([], $result['items']);
    }

    private function createVariantWithStock(?int $quantity): ProductVariant&MockObject
    {
        $variant = $this->createMock(ProductVariant::class);

        if ($quantity === null) {
            $variant->method('getInStock')->willReturn(null);
            $variant->method('getStock')->willReturn(null);
            $variant->method('getStatus')->willReturn(ProductVariantStatus::AVAILABLE);
            $variant->method('getEans')->willReturn(new ArrayCollection([]));
        } elseif ($quantity === 0) {
            $stock = $this->createMock(ProductVariantStock::class);
            $stock->method('getQuantityAvailable')->willReturn(0);
            $stock->method('getStatus')->willReturn(ProductStockStatus::IN_STOCK);
            $variant->method('getInStock')->willReturn(null);
            $variant->method('getStock')->willReturn($stock);
            $variant->method('getStatus')->willReturn(ProductVariantStatus::AVAILABLE);
            $variant->method('getEans')->willReturn(new ArrayCollection([]));
        } else {
            $stock = $this->createMock(ProductVariantStock::class);
            $stock->method('getQuantityAvailable')->willReturn($quantity);
            $stock->method('getStatus')->willReturn(ProductStockStatus::IN_STOCK);

            $ean = $this->createMock(ProductVariantEan::class);
            $ean->method('getStatus')->willReturn(ProductVariantEanStatus::ACTIVE);

            $variant->method('getInStock')->willReturn($stock);
            $variant->method('getStock')->willReturn($stock);
            $variant->method('getStatus')->willReturn(ProductVariantStatus::AVAILABLE);
            $variant->method('getEans')->willReturn(new ArrayCollection([$ean]));
        }

        return $variant;
    }

    private function createItemWithVariant(ProductVariant $variant): CartItem&MockObject
    {
        $item = $this->createMock(CartItem::class);
        $item->method('getVariant')->willReturn($variant);

        return $item;
    }

    /**
     * @param object[] $items
    */
    private function createCartWithItems(array $items): Cart
    {
        $cart = $this->createMock(Cart::class);
        $cart->method('getItems')->willReturn(new ArrayCollection($items));

        return $cart;
    }
}
