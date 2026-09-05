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
    Segment\Cart\Enum\CartAction,
    Segment\Product\Entity\Variant\ProductVariant
};

use App\Core\Application\Segment\Cart\Service\Query\CartItemQueryService;

use App\Core\Ports\{
    Segment\Cart\Repository\CartItemRepositoryContract,
    Segment\Cart\Repository\CartRepositoryContract,
    Segment\Cart\Service\Query\CartControlQueryContract,
    Segment\Cart\Service\Query\CartItemQueryContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Segment\Product\Repository\Variant\ProductVariantEanRepositoryContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract
};

use Tests\Support\Stub\UserStub;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Query\CartItemQueryService
*/
final class CartItemQueryServiceTest extends TestCase
{
    use UserStub;

    private ProductVariantRepositoryContract&MockObject $variantRepository;
    private ProductVariantEanRepositoryContract&MockObject $variantEanRepository;
    private CartRepositoryContract&MockObject $cartRepository;
    private CartControlQueryContract&MockObject $cartControlQuery;
    private CartItemRepositoryContract&MockObject $cartItemRepository;
    private CartRenderQueryContract&MockObject $cartRenderQuery;
    private CartItemQueryService $service;
    private ProductVariant&MockObject $variant;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->variant = $this->createMock(ProductVariant::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartItemQueryContract::class, $this->service);
    }

    public function testGetItemsReturnsEmptyArrayWhenCartNotFound(): void
    {
        $user = $this->createUserWithId(1);

        $this->cartRepository->method('findCartForUser')->willReturn(null);

        $result = $this->service->getItems($user);

        $this->assertSame([], $result);
    }

    public function testGetItemsReturnsCartItems(): void
    {
        $user = $this->createUserWithId(1);
        $item = $this->createMock(CartItem::class);
        $cart = $this->createCartWithItems([$item]);

        $this->cartRepository->method('findCartForUser')->willReturn($cart);

        $result = $this->service->getItems($user);

        $this->assertCount(1, $result);
        $this->assertSame($item, $result[0]);
    }

    public function testGetCartAndVariantReturnsCartAndVariant(): void
    {
        $user = $this->createUserWithId(1);
        $variant = $this->createMock(ProductVariant::class);
        $variant->method('getId')->willReturn(10);
        $cart = $this->createMock(Cart::class);

        $this->variantRepository->method('findById')->with(10)->willReturn($variant);
        $this->cartControlQuery
            ->method('getUserCart')
            ->willReturn($cart);

        $result = $this->service->getCartAndVariant($user, 10);

        $this->assertSame($cart, $result['cart']);
        $this->assertSame($variant, $result['variant']);
    }

    public function testGetValidatedCartItemReturnsItem(): void
    {
        $item = $this->createMock(CartItem::class);

        $this->cartItemRepository->method('getItem')->with(5)->willReturn($item);

        $result = $this->service->getValidatedCartItem(5);

        $this->assertSame($item, $result);
    }

    public function testGetAvailableEansCountReturnsZeroWhenEmpty(): void
    {
        $this->variantEanRepository->method('findAvailableByVariant')->willReturn([]);

        $result = $this->service->getAvailableEansCount($this->variant);

        $this->assertSame(0, $result);
    }

    public function testGetAvailableEansCountReturnsCount(): void
    {
        $ean1 = new \stdClass();
        $ean2 = new \stdClass();

        $this->variantEanRepository->method('findAvailableByVariant')->willReturn([$ean1, $ean2]);

        $result = $this->service->getAvailableEansCount($this->variant);

        $this->assertSame(2, $result);
    }

    public function testGetExistingQuantityReturnsZeroWhenNoMatchingVariant(): void
    {
        $cart = $this->createMock(Cart::class);
        $item = $this->createMock(CartItem::class);

        $item->method('hasVariant')->willReturn(false);
        $cart->method('getItems')->willReturn(new ArrayCollection([$item]));

        $result = $this->service->getExistingQuantity($cart, $this->variant);

        $this->assertSame(0, $result);
    }

    public function testGetExistingQuantityCountsMatchingVariants(): void
    {
        $cart = $this->createMock(Cart::class);

        $itemA = $this->createMock(CartItem::class);
        $itemB = $this->createMock(CartItem::class);
        $itemC = $this->createMock(CartItem::class);

        $itemA->method('hasVariant')->willReturn(true);
        $itemB->method('hasVariant')->willReturn(true);
        $itemC->method('hasVariant')->willReturn(false);

        $cart->method('getItems')->willReturn(new ArrayCollection([$itemA, $itemB, $itemC]));

        $result = $this->service->getExistingQuantity($cart, $this->variant);

        $this->assertSame(2, $result);
    }

    public function testBuildCartResponseDelegatesToCartRenderQuery(): void
    {
        $expected = $this->buildExpected(totalItems: 1, totalPrice: '10,00 €', message: 'Product added to cart.');

        $result = $this->buildCartResponse(CartAction::ADD, 'Product added to cart.', $expected);

        $this->assertSame($expected, $result);
    }

    public function testBuildCartResponseUsesRemoveMessage(): void
    {
        $expected = $this->buildExpected(totalItems: 0, totalPrice: '0,00 €', message: 'Product removed from cart.');

        $result = $this->buildCartResponse(CartAction::REMOVE, 'Product removed from cart.', $expected);

        $this->assertSame($expected, $result);
    }

    private function initMocks(): void
    {
        $this->variantRepository = $this->createMock(ProductVariantRepositoryContract::class);
        $this->variantEanRepository = $this->createMock(ProductVariantEanRepositoryContract::class);
        $this->cartRepository = $this->createMock(CartRepositoryContract::class);
        $this->cartControlQuery = $this->createMock(CartControlQueryContract::class);
        $this->cartItemRepository = $this->createMock(CartItemRepositoryContract::class);
        $this->cartRenderQuery = $this->createMock(CartRenderQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartItemQueryService(
            $this->variantRepository,
            $this->variantEanRepository,
            $this->cartRepository,
            $this->cartControlQuery,
            $this->cartItemRepository,
            $this->cartRenderQuery,
        );
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

    /**
     * @return array<string, mixed>
    */
    private function buildExpected(int $totalItems, string $totalPrice, string $message): array
    {
        return [
            'html'       => '<div>',
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
            'message'    => $message,
            'success'    => true,
            'status'     => null,
        ];
    }

    /**
     * @param array<string, mixed> $expected
     * 
     * @return array<string, mixed>
    */
    private function buildCartResponse(CartAction $action, string $message, array $expected): array
    {
        $user = $this->createUserWithId(1);

        $this->cartRenderQuery
            ->expects($this->once())
            ->method('buildCartResponse')
            ->with($user, $message)
            ->willReturn($expected);

        return $this->service->buildCartResponse($user, $action);
    }
}
