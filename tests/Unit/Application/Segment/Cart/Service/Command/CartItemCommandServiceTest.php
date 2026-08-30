<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Cart\Service\Command;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem,
    Segment\Cart\Enum\CartAction,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\User\Entity\User
};

use App\Core\Application\Segment\Cart\Service\Command\CartItemCommandService;

use App\Core\Ports\{
    Segment\Cart\Policy\CartItemAvailabilityPolicyContract,
    Segment\Cart\Service\Command\CartControlCommandContract,
    Segment\Cart\Service\Command\CartItemCommandContract,
    Segment\Cart\Service\Query\CartItemQueryContract,
    Segment\Cart\Service\Query\CartRenderQueryContract,
    Shared\Persistence\EntityPersistenceContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Command\CartItemCommandService
*/
class CartItemCommandServiceTest extends TestCase
{
    private EntityPersistenceContract&MockObject $entityPersistence;
    private CartItemAvailabilityPolicyContract&MockObject $cartItemPolicy;
    private CartItemQueryContract&MockObject $cartItemQuery;
    private CartRenderQueryContract&MockObject $cartRenderQuery;
    private CartControlCommandContract&MockObject $cartControlCommand;
    private CartItemCommandService $service;
    private User&MockObject $user;
    private Cart&MockObject $cart;
    private ProductVariant&MockObject $variant;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();

        $this->user = $this->createMock(User::class);
        $this->cart = $this->createMock(Cart::class);
        $this->variant = $this->createMock(ProductVariant::class);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartItemCommandContract::class, $this->service);
    }

    public function testAddProductToCartReturnsErrorWhenNotAvailable(): void
    {
        $this->setupCartAndVariant();
        $this->cartItemPolicy->method('isAvailable')->willReturn(false);

        $expected = [
            'html'       => '',
            'totalItems' => 0,
            'totalPrice' => '0,00 €',
            'message'    => 'This product is no longer in stock.',
            'success'    => false,
            'status'     => 422,
        ];

        $this->cartRenderQuery
            ->expects($this->once())
            ->method('buildCartResponse')
            ->with($this->user, 'This product is no longer in stock.', true)
            ->willReturn($expected);

        $result = $this->service->addProductToCart($this->user, 1);

        $this->assertSame($expected, $result);
    }

    public function testAddProductToCartPersistsItemWhenAvailable(): void
    {
        $this->setupAvailableProduct();

        $this->entityPersistence
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(CartItem::class), true);

        $this->service->addProductToCart($this->user, 1);
    }

    public function testAddProductToCartRefreshesCartWhenAvailable(): void
    {
        $this->setupAvailableProduct();

        $this->cartControlCommand
            ->expects($this->once())
            ->method('flushAndRefreshCart')
            ->with($this->cart);

        $this->service->addProductToCart($this->user, 1);
    }

    public function testAddProductToCartReturnsBuildCartResponse(): void
    {
        $this->setupCartAndVariant();
        $this->cartItemPolicy->method('isAvailable')->willReturn(true);

        $expected = ['success' => true, 'message' => 'Product added to cart.'];

        $this->cartItemQuery
            ->expects($this->once())
            ->method('buildCartResponse')
            ->with($this->user, CartAction::ADD)
            ->willReturn($expected);

        $result = $this->service->addProductToCart($this->user, 1);

        $this->assertSame($expected, $result);
    }

    public function testRemoveProductFromCartRemovesItemAndReturnsResponse(): void
    {
        $item = $this->createMock(CartItem::class);
        $item->method('getCart')->willReturn($this->cart);

        $this->cartItemQuery->method('getValidatedCartItem')->with(5)->willReturn($item);

        $this->entityPersistence
            ->expects($this->once())
            ->method('remove')
            ->with($item);

        $expected = ['success' => true, 'message' => 'Product removed from cart.'];

        $this->cartItemQuery
            ->method('buildCartResponse')
            ->with($this->user, CartAction::REMOVE)
            ->willReturn($expected);

        $result = $this->service->removeProductFromCart($this->user, 5);

        $this->assertSame($expected, $result);
    }

    public function testRemoveProductFromCartRefreshesCartWhenCartExists(): void
    {
        $this->mockRemoveItem($this->cart);

        $this->cartControlCommand
            ->expects($this->once())
            ->method('flushAndRefreshCart')
            ->with($this->cart);

        $this->service->removeProductFromCart($this->user, 5);
    }

    public function testRemoveProductFromCartSkipsRefreshWhenCartIsNull(): void
    {
        $this->mockRemoveItem(null);

        $this->cartControlCommand
            ->expects($this->never())
            ->method('flushAndRefreshCart');

        $this->service->removeProductFromCart($this->user, 5);
    }

    public function testRemoveVariantRemovesMatchingCartItems(): void
    {
        $itemA = $this->createMock(CartItem::class);
        $itemB = $this->createMock(CartItem::class);
        $itemC = $this->createMock(CartItem::class);

        $itemA->method('hasVariant')->willReturn(true);
        $itemB->method('hasVariant')->willReturn(false);
        $itemC->method('hasVariant')->willReturn(true);

        $this->entityPersistence
            ->expects($this->exactly(2))
            ->method('remove');

        $this->entityPersistence
            ->expects($this->once())
            ->method('flush');

        $this->service->removeVariant($this->variant, [$itemA, $itemB, $itemC]);
    }

    public function testRemoveVariantFlushesAfterRemoving(): void
    {
        $this->entityPersistence
            ->expects($this->once())
            ->method('flush');

        $this->service->removeVariant($this->variant, []);
    }

    public function testRemoveVariantRefreshesCartWhenMatchingItemHasCart(): void
    {
        $item = $this->createMock(CartItem::class);
        $item->method('hasVariant')->willReturn(true);
        $item->method('getCart')->willReturn($this->cart);

        $this->cartControlCommand
            ->expects($this->once())
            ->method('flushAndRefreshCart')
            ->with($this->cart);

        $this->service->removeVariant($this->variant, [$item]);
    }

    public function testRemoveVariantDoesNotRefreshCartWhenNoItemsMatch(): void
    {
        $item = $this->createMock(CartItem::class);
        $item->method('hasVariant')->willReturn(false);

        $this->cartControlCommand
            ->expects($this->never())
            ->method('flushAndRefreshCart');

        $this->service->removeVariant($this->variant, [$item]);
    }

    private function initMocks(): void
    {
        $this->entityPersistence = $this->createMock(EntityPersistenceContract::class);
        $this->cartItemPolicy = $this->createMock(CartItemAvailabilityPolicyContract::class);
        $this->cartItemQuery = $this->createMock(CartItemQueryContract::class);
        $this->cartRenderQuery = $this->createMock(CartRenderQueryContract::class);
        $this->cartControlCommand = $this->createMock(CartControlCommandContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartItemCommandService(
            $this->entityPersistence,
            $this->cartItemPolicy,
            $this->cartItemQuery,
            $this->cartRenderQuery,
            $this->cartControlCommand,
        );
    }

    private function setupCartAndVariant(): void
    {
        $this->cartItemQuery
            ->method('getCartAndVariant')
            ->willReturn(['cart' => $this->cart, 'variant' => $this->variant]);
    }

    private function setupAvailableProduct(): void
    {
        $this->setupCartAndVariant();
        $this->cartItemPolicy->method('isAvailable')->willReturn(true);
        $this->cartItemQuery->method('buildCartResponse')->willReturn(['success' => true]);
    }

    private function mockRemoveItem(?Cart $cart): void
    {
        $item = $this->createMock(CartItem::class);
        $item->method('getCart')->willReturn($cart);

        $this->cartItemQuery->method('getValidatedCartItem')->willReturn($item);
        $this->cartItemQuery->method('buildCartResponse')->willReturn([]);
    }
}
