<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\Framework\TestCase;

use App\Core\Application\Segment\Product\Service\Query\ProductRouteQueryService;

use App\Core\Ports\Segment\Product\Service\Query\ProductRouteQueryContract;

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Service\Query\ProductRouteQueryService
*/
final class ProductRouteQueryServiceTest extends TestCase
{
    private ProductRouteQueryService $service;

    protected function setUp(): void
    {
        $this->service = new ProductRouteQueryService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductRouteQueryContract::class, $this->service);
    }

    public function testGenerateRouteReturnsProductsIndexForProductsPath(): void
    {
        $this->assertSame('products_index', $this->service->generateRoute('/products'));
    }

    public function testGenerateRouteReturnsProductsIndexForProductsSubPath(): void
    {
        $this->assertSame('products_index', $this->service->generateRoute('/products/category/electronics'));
    }

    public function testGenerateRouteReturnsEmptyForProductShowPath(): void
    {
        $this->assertSame('', $this->service->generateRoute('/product/show'));
    }

    public function testGenerateRouteReturnsDiscountsForDiscountPath(): void
    {
        $this->assertSame('discounts', $this->service->generateRoute('/discounts'));
    }

    public function testGenerateRouteReturnsDiscountsForDiscountSubPath(): void
    {
        $this->assertSame('discounts', $this->service->generateRoute('/discounts/shoes'));
    }

    public function testGenerateRouteReturnsEmptyForUnknownPath(): void
    {
        $this->assertSame('', $this->service->generateRoute('/some/other/path'));
    }

    public function testGenerateRouteReturnsEmptyForEmptyPath(): void
    {
        $this->assertSame('', $this->service->generateRoute(''));
    }

    public function testGenerateRouteReturnsEmptyForRootPath(): void
    {
        $this->assertSame('', $this->service->generateRoute('/'));
    }
}
