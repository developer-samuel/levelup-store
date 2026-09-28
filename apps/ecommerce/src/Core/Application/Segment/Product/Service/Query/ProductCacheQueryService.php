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

    /**
     * @param ProductTitleQueryContract $productTitleQuery
     * @param ProductRouteQueryContract $productRouteQuery
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(
        private readonly ProductTitleQueryContract $productTitleQuery,
        private readonly ProductRouteQueryContract $productRouteQuery,
        CacheGatewayContract $cacheGateway,
    ) {
        $this->titleCache = $cacheGateway->getCache(ProductCachePool::TITLE->value);
        $this->routeCache = $cacheGateway->getCache(ProductCachePool::ROUTE->value);
    }

    /**
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscount
     *
     * @return string
    */
    public function getTitle(
        ?string $category,
        ?string $type,
        bool $isDiscount,
    ): string {
        $cacheKey = $this->getTitleCacheKey($category, $type, $isDiscount);

        return $this->fetchTitleCachedData($cacheKey, $category, $type, $isDiscount);
    }

    /**
     * @param string $path
     *
     * @return string
    */
    public function getRoute(string $path): string
    {
        $cacheKey = $this->getRouteCacheKey($path);

        return $this->fetchRouteCachedData($cacheKey, $path);
    }

    /**
     * @param string $cacheKey
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscount
     *
     * @return string
    */
    private function fetchTitleCachedData(string $cacheKey, ?string $category, ?string $type, bool $isDiscount): string
    {
        $data = $this->titleCache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): string =>
                $this->titleCacheCallback($item, $category, $type, $isDiscount),
        );

        return is_string($data) ? $data : '';
    }

    /**
     * @return string
    */
    private function fetchRouteCachedData(string $cacheKey, string $path): string
    {
        $data = $this->routeCache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): string =>
                $this->routeCacheCallback($item, $path),
        );

        return is_string($data) ? $data : '';
    }

    /**
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscount
     *
     * @return string
    */
    private function getTitleCacheKey(?string $category, ?string $type, bool $isDiscount): string
    {
        return ProductCacheKeyPrefix::TITLE->value
            . md5(($category ?? '') . ($type ?? '') . ($isDiscount ? '1' : '0'));
    }

    /**
     * @param string $path
     *
     * @return string
    */
    private function getRouteCacheKey(string $path): string
    {
        return ProductCacheKeyPrefix::ROUTE->value . md5($path);
    }

    /**
     * @param CacheItemProxyContract $item
     * @param string|null $category
     * @param string|null $type
     * @param bool $isDiscount
     *
     * @return string
    */
    private function titleCacheCallback(CacheItemProxyContract $item, ?string $category, ?string $type, bool $isDiscount): string
    {
        $item->expiresAfter(CacheTTLConstants::FIVE_MINUTES);

        return $this->productTitleQuery->generateTitle($category, $type, $isDiscount);
    }

    /**
     * @param CacheItemProxyContract $item
     * @param string $path
     *
     * @return string
    */
    private function routeCacheCallback(CacheItemProxyContract $item, string $path): string
    {
        $item->expiresAfter(CacheTTLConstants::FIVE_MINUTES);

        return $this->productRouteQuery->generateRoute($path);
    }
}
