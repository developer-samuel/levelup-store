<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Segment\Product\Service\Query;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Application\Segment\Product\Service\Query\ProductCacheQueryService;

use App\Core\Ports\{
    Gateways\Internal\Cache\CacheGatewayContract,
    Segment\Product\Service\Query\ProductCacheQueryContract,
    Segment\Product\Service\Query\ProductRouteQueryContract,
    Segment\Product\Service\Query\ProductTitleQueryContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

/**
 * @coversDefaultClass \App\Core\Application\Segment\Product\Service\Query\ProductCacheQueryService
*/
final class ProductCacheQueryServiceTest extends TestCase
{
    private ProductTitleQueryContract&MockObject $productTitleQuery;
    private ProductRouteQueryContract&MockObject $productRouteQuery;
    private CacheGatewayContract&MockObject $cacheGateway;
    private CacheProxyContract&MockObject $titleCache;
    private CacheProxyContract&MockObject $routeCache;
    private ProductCacheQueryService $service;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initService();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(ProductCacheQueryContract::class, $this->service);
    }

    public function testGetTitleReturnsCachedTitle(): void
    {
        $this->titleCache
            ->method('get')
            ->willReturnCallback(fn(string $key, callable $cb): string => 'Electronics');

        $result = $this->service->getTitle('electronics', null, false);

        $this->assertSame('Electronics', $result);
    }

    public function testGetTitleDelegatesToProductTitleQuery(): void
    {
        $this->productTitleQuery
            ->expects($this->once())
            ->method('generateTitle')
            ->with('electronics', null, false)
            ->willReturn('Electronics');

        $this->titleCache->method('get')->willReturnCallback($this->makeCacheInvokerCallback());

        $result = $this->service->getTitle('electronics', null, false);

        $this->assertSame('Electronics', $result);
    }

    public function testGetTitleReturnsEmptyStringWhenCacheReturnsNonString(): void
    {
        $this->titleCache
            ->method('get')
            ->willReturn(null);

        $result = $this->service->getTitle('electronics', null, false);

        $this->assertSame('', $result);
    }

    public function testGetTitleSetsExpiry(): void
    {
        $item = $this->createMock(CacheItemProxyContract::class);
        $item->expects($this->once())->method('expiresAfter');

        $this->productTitleQuery->method('generateTitle')->willReturn('Electronics');
        $this->titleCache->method('get')->willReturnCallback($this->makeCacheInvokerCallback($item));

        $this->service->getTitle('electronics', null, false);
    }

    public function testGetRouteReturnsCachedRoute(): void
    {
        $this->routeCache
            ->method('get')
            ->willReturnCallback(fn(string $key, callable $cb): string => 'products_index');

        $result = $this->service->getRoute('/products');

        $this->assertSame('products_index', $result);
    }

    public function testGetRouteDelegatesToProductRouteQuery(): void
    {
        $this->productRouteQuery
            ->expects($this->once())
            ->method('generateRoute')
            ->with('/products')
            ->willReturn('products_index');

        $this->routeCache->method('get')->willReturnCallback($this->makeCacheInvokerCallback());

        $result = $this->service->getRoute('/products');

        $this->assertSame('products_index', $result);
    }

    public function testGetRouteReturnsEmptyStringWhenCacheReturnsNonString(): void
    {
        $this->routeCache
            ->method('get')
            ->willReturn(null);

        $result = $this->service->getRoute('/products');

        $this->assertSame('', $result);
    }

    public function testGetRouteSetsExpiry(): void
    {
        $item = $this->createMock(CacheItemProxyContract::class);
        $item->expects($this->once())->method('expiresAfter');

        $this->productRouteQuery->method('generateRoute')->willReturn('products_index');
        $this->routeCache->method('get')->willReturnCallback($this->makeCacheInvokerCallback($item));

        $this->service->getRoute('/products');
    }

    private function initMocks(): void
    {
        $this->productTitleQuery = $this->createMock(ProductTitleQueryContract::class);
        $this->productRouteQuery = $this->createMock(ProductRouteQueryContract::class);
        $this->titleCache = $this->createMock(CacheProxyContract::class);
        $this->routeCache = $this->createMock(CacheProxyContract::class);
        $this->cacheGateway = $this->createMock(CacheGatewayContract::class);

        $this->cacheGateway
            ->method('getCache')
            ->willReturnCallback(function (string $namespace): CacheProxyContract {
                return match ($namespace) {
                    'product_title_cache' => $this->titleCache,
                    default               => $this->routeCache,
                };
            });
    }

    private function initService(): void
    {
        $this->service = new ProductCacheQueryService(
            $this->productTitleQuery,
            $this->productRouteQuery,
            $this->cacheGateway,
        );
    }

    private function makeCacheInvokerCallback(?CacheItemProxyContract $item = null): \Closure
    {
        return function (string $key, callable $cb) use ($item): string {
            $cacheItem = $item ?? $this->createMock(CacheItemProxyContract::class);

            $result = $cb($cacheItem);
            assert(is_string($result));

            return $result;
        };
    }
}
