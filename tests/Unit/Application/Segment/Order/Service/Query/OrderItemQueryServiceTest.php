<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Order\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\{
    Segment\Cart\Entity\CartItem,
    Segment\Product\Entity\Product,
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantStock,
    Segment\Order\ValueObject\Stripe\StripeLineItemObject
};

use App\Core\Application\Segment\Order\Service\Query\OrderItemQueryService;

use App\Core\Ports\Segment\Order\Service\Query\OrderItemQueryContract;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Order\Service\Query\OrderItemQueryService
*/
final class OrderItemQueryServiceTest extends TestCase
{
    private OrderItemQueryService $service;

    protected function setUp(): void
    {
        $this->service = new OrderItemQueryService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(OrderItemQueryContract::class, $this->service);
    }

    public function testPrepareLineItemsReturnsEmptyArrayForNoItems(): void
    {
        $result = $this->service->prepareLineItems([]);

        $this->assertSame([], $result);
    }

    public function testPrepareLineItemsReturnsSingleItemForOneCartItem(): void
    {
        $result = $this->service->prepareLineItems([$this->buildDefaultCartItem()]);

        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(StripeLineItemObject::class, $result);
    }

    public function testPrepareLineItemsGroupsDuplicateVariantsIntoQuantity(): void
    {
        $item = $this->buildDefaultCartItem();

        $result = array_values($this->service->prepareLineItems([$item, $item]));

        $this->assertCount(1, $result);
        $this->assertSame(2, $result[0]->quantity);
    }

    public function testPrepareLineItemsReturnsOneItemPerUniqueVariant(): void
    {
        $cartItems = [
            $this->buildCartItem(variantId: 1, price: 10.0, productName: 'Widget A'),
            $this->buildCartItem(variantId: 2, price: 20.0, productName: 'Widget B'),
        ];

        $this->assertCount(2, $this->service->prepareLineItems($cartItems));
    }

    public function testPrepareLineItemsConvertsUnitAmountToCents(): void
    {
        $lineItem = $this->firstLineItem($this->buildCartItem(variantId: 1, price: 9.99, productName: 'Widget'));

        $this->assertSame(999, $lineItem->price->unitAmount);
    }

    public function testPrepareLineItemsUsesProductName(): void
    {
        $lineItem = $this->firstLineItem($this->buildCartItem(variantId: 1, price: 10.0, productName: 'Super Widget'));

        $this->assertSame('Super Widget', $lineItem->price->productName);
    }

    public function testPrepareLineItemsUsesCurrencyEur(): void
    {
        $lineItem = $this->firstLineItem($this->buildDefaultCartItem());

        $this->assertSame('eur', $lineItem->price->currency);
    }

    public function testIsStockAvailableReturnsTrueWhenAvailable(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $stock->method('isAvailable')->willReturn(true);

        $this->assertTrue($this->service->isStockAvailable($stock));
    }

    public function testIsStockAvailableReturnsFalseWhenNotAvailable(): void
    {
        $stock = $this->createMock(ProductVariantStock::class);
        $stock->method('isAvailable')->willReturn(false);

        $this->assertFalse($this->service->isStockAvailable($stock));
    }

    public function testIsStockAvailableReturnsFalseWhenNull(): void
    {
        $this->assertFalse($this->service->isStockAvailable(null));
    }

    private function buildDefaultCartItem(): CartItem&MockObject
    {
        return $this->buildCartItem(variantId: 1, price: 10.0, productName: 'Widget');
    }

    private function firstLineItem(CartItem $cartItem): StripeLineItemObject
    {
        return array_values($this->service->prepareLineItems([$cartItem]))[0];
    }

    private function buildCartItem(int $variantId, float $price, string $productName): CartItem&MockObject
    {
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn($productName);

        $variant = $this->createMock(ProductVariant::class);
        $variant->method('getId')->willReturn($variantId);
        $variant->method('getName')->willReturn($productName);
        $variant->method('getProduct')->willReturn($product);
        $variant->method('getDiscountedPrice')->willReturn($price);

        $cartItem = $this->createMock(CartItem::class);
        $cartItem->method('getVariant')->willReturn($variant);

        return $cartItem;
    }
}
