<?php

declare(strict_types=1);

namespace App\Core\Application\Home;

use App\Core\Domain\Home\HomeCacheObject;

use App\Core\Application\{
    Segment\Banner\BannerResource,
    Shared\Constants\CacheTTLConstants,
    Shared\Utils\Mapper\ResourceMapper
};

use App\Core\Ports\{
    Home\HomeCacheQueryContract,
    Gateways\Internal\Cache\CacheGatewayContract,
    Segment\Banner\BannerRepositoryContract,
    Segment\Product\Service\Query\ProductRecommendedQueryContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final readonly class HomeCacheQueryService implements HomeCacheQueryContract
{
    private const CACHE_POOL = 'home_cache';
    private const CACHE_KEY = 'home_prefix';

    private CacheProxyContract $cache;

    /**
     * @param BannerRepositoryContract $bannerRepository
     * @param ProductRecommendedQueryContract $productRecommendedQuery
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(
        private BannerRepositoryContract $bannerRepository,
        private ProductRecommendedQueryContract $productRecommendedQuery,
        CacheGatewayContract $cacheGateway,
    ) {
        $this->cache = $cacheGateway->getCache(self::CACHE_POOL);
    }

    /**
     * @return array<string, mixed>
    */
    public function getHomeData(): array
    {
        $cacheKey = self::CACHE_KEY;

        $data = $this->fetchCachedData($cacheKey);
        if (!$data instanceof HomeCacheObject) {
            return [];
        }

        return $data->toArray();
    }

    /**
     * @param string $cacheKey
     *
     * @return mixed
    */
    private function fetchCachedData(string $cacheKey): mixed
    {
        return $this->cache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): HomeCacheObject =>
                $this->cacheCallback($item),
        );
    }

    /**
     * @param CacheItemProxyContract $item
     *
     * @return HomeCacheObject
    */
    private function cacheCallback(CacheItemProxyContract $item): HomeCacheObject
    {
        $this->configureCacheItem($item);

        return new HomeCacheObject(
            $this->productRecommendedQuery->findAll(),
            $this->getMappedBanners(),
        );
    }

    /**
     * @param CacheItemProxyContract $item
     *
     * @return void
    */
    private function configureCacheItem(CacheItemProxyContract $item): void
    {
        $item->expiresAfter(CacheTTLConstants::FIVE_MINUTES);
    }

    /**
     * @return list<array<string, mixed>>
    */
    private function getMappedBanners(): array
    {
        return ResourceMapper::collection(
            $this->bannerRepository->findAllActive(),
            BannerResource::class,
        );
    }
}
