<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\Framework\TestCase;

use App\Core\Application\Segment\Product\Service\Query\ProductRouteQueryService;

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

    public function testGenerateRouteReturnsProductsIndexForProductsPath(): void
    {
        self::assertSame('products_index', $this->service->generateRoute('/products'));
    }

    public function testGenerateRouteReturnsProductsIndexForProductsSubPath(): void
    {
        self::assertSame('products_index', $this->service->generateRoute('/products/category/electronics'));
    }

    public function testGenerateRouteReturnsEmptyForProductShowPath(): void
    {
        self::assertSame('', $this->service->generateRoute('/product/show'));
    }

    public function testGenerateRouteReturnsDiscountsForDiscountPath(): void
    {
        self::assertSame('discounts', $this->service->generateRoute('/discounts'));
    }

    public function testGenerateRouteReturnsDiscountsForDiscountSubPath(): void
    {
        self::assertSame('discounts', $this->service->generateRoute('/discounts/shoes'));
    }

    public function testGenerateRouteReturnsEmptyForUnknownPath(): void
    {
        self::assertSame('', $this->service->generateRoute('/some/other/path'));
    }

    public function testGenerateRouteReturnsEmptyForEmptyPath(): void
    {
        self::assertSame('', $this->service->generateRoute(''));
    }

    public function testGenerateRouteReturnsEmptyForRootPath(): void
    {
        self::assertSame('', $this->service->generateRoute('/'));
    }
}
