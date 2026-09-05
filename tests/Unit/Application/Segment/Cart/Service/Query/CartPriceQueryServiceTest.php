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
    Segment\Product\ValueObject\ProductPriceObject
};

use App\Core\Application\Segment\Cart\Service\Query\CartPriceQueryService;

use App\Core\Ports\{
    Segment\Cart\Service\Query\CartPriceQueryContract,
    Segment\Product\Service\Query\ProductPriceQueryContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Cart\Service\Query\CartPriceQueryService
*/
final class CartPriceQueryServiceTest extends TestCase
{
    private ProductPriceQueryContract&MockObject $productPriceQuery;
    private CartPriceQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(CartPriceQueryContract::class, $this->service);
    }

    public function testCalculateTotalPriceReturnsZeroForEmptyItems(): void
    {
        $result = $this->service->calculateTotalPrice([]);

        $this->assertSame(0.0, $result);
    }

    public function testCalculateTotalPriceSumsDiscountedPrices(): void
    {
        $variantA = $this->createMock(ProductVariant::class);
        $variantB = $this->createMock(ProductVariant::class);

        $itemA = $this->createCartItemObject($variantA);
        $itemB = $this->createCartItemObject($variantB);

        $this->productPriceQuery
            ->method('getPrice')
            ->willReturnMap([
                [$variantA, new ProductPriceObject(100.0, 80.0, true)],
                [$variantB, new ProductPriceObject(50.0, 50.0, false)],
            ]);

        $result = $this->service->calculateTotalPrice([$itemA, $itemB]);

        $this->assertSame(130.0, $result);
    }

    public function testCalculateTotalPriceUsesDiscountedPriceNotOriginal(): void
    {
        $result = $this->calculateWithSingleItem(new ProductPriceObject(100.0, 60.0, true));

        $this->assertSame(60.0, $result);
        $this->assertNotSame(100.0, $result);
    }

    public function testCalculateTotalPriceWithSingleItem(): void
    {
        $result = $this->calculateWithSingleItem(new ProductPriceObject(99.99, 99.99, false));

        $this->assertSame(99.99, $result);
    }

    private function initMocks(): void
    {
        $this->productPriceQuery = $this->createMock(ProductPriceQueryContract::class);
    }

    private function initService(): void
    {
        $this->service = new CartPriceQueryService($this->productPriceQuery);
    }

    private function createCartItemObject(ProductVariant $variant): CartItemObject
    {
        return new CartItemObject(
            id: 1,
            cartId: 1,
            variant: $variant,
            formattedPrice: '99,99 €',
            hasDiscount: false,
            averageRating: 0.0,
        );
    }

    private function calculateWithSingleItem(ProductPriceObject $price): float
    {
        $variant = $this->createMock(ProductVariant::class);

        $this->productPriceQuery->method('getPrice')->willReturn($price);

        return $this->service->calculateTotalPrice([$this->createCartItemObject($variant)]);
    }
}
