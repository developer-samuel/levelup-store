<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Service\Query;

use App\Core\Application\{
    Segment\Product\Enum\ProductCacheKeyPrefix,
    Segment\Product\Enum\ProductCachePool,
    Shared\Constants\CacheTTLConstants
};

use App\Core\Ports\{
    Gateways\Internal\Cache\CacheGatewayContract,
    Segment\Product\Service\Query\ProductCacheQueryContract,
    Segment\Product\Service\Query\ProductRouteQueryContract,
    Segment\Product\Service\Query\ProductTitleQueryContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final class ProductCacheQueryService implements ProductCacheQueryContract
{
    private CacheProxyContract $titleCache;
    private CacheProxyContract $routeCache;

    public function __construct(
        private readonly ProductTitleQueryContract $productTitleQuery,
        private readonly ProductRouteQueryContract $productRouteQuery,
        CacheGatewayContract $cacheGateway,
    ) {
        $this->titleCache = $cacheGateway->getCache(ProductCachePool::TITLE->value);
        $this->routeCache = $cacheGateway->getCache(ProductCachePool::ROUTE->value);
    }

    public function getTitle(
        ?string $category,
        ?string $type,
        bool $isDiscount,
    ): string {
        $cacheKey = $this->getTitleCacheKey($category, $type, $isDiscount);

        return $this->fetchTitleCachedData($cacheKey, $category, $type, $isDiscount);
    }

    public function getRoute(string $path): string
    {
        $cacheKey = $this->getRouteCacheKey($path);

        return $this->fetchRouteCachedData($cacheKey, $path);
    }

    private function fetchTitleCachedData(string $cacheKey, ?string $category, ?string $type, bool $isDiscount): string
    {
        $data = $this->titleCache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): string =>
                $this->titleCacheCallback($item, $category, $type, $isDiscount),
        );

        return is_string($data) ? $data : '';
    }

    private function fetchRouteCachedData(string $cacheKey, string $path): string
    {
        $data = $this->routeCache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): string =>
                $this->routeCacheCallback($item, $path),
        );

        return is_string($data) ? $data : '';
    }

    private function getTitleCacheKey(?string $category, ?string $type, bool $isDiscount): string
    {
        return ProductCacheKeyPrefix::TITLE->value
            . md5(($category ?? '') . ($type ?? '') . ($isDiscount ? '1' : '0'));
    }

    private function getRouteCacheKey(string $path): string
    {
        return ProductCacheKeyPrefix::ROUTE->value . md5($path);
    }

    private function titleCacheCallback(CacheItemProxyContract $item, ?string $category, ?string $type, bool $isDiscount): string
    {
        $item->expiresAfter(CacheTTLConstants::FIVE_MINUTES);

        return $this->productTitleQuery->generateTitle($category, $type, $isDiscount);
    }

    private function routeCacheCallback(CacheItemProxyContract $item, string $path): string
    {
        $item->expiresAfter(CacheTTLConstants::FIVE_MINUTES);

        return $this->productRouteQuery->generateRoute($path);
    }
}
