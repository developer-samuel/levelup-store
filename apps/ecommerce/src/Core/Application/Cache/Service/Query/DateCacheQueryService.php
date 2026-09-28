<?php

declare(strict_types=1);

namespace App\Core\Application\Cache\Service\Query;

use Kit\Assertion\Shared\CacheAssertion;

use App\Core\Domain\Cache\DateCacheObject;

use App\Core\Application\Shared\Constants\CacheTTLConstants;

use App\Core\Ports\{
    Cache\Service\Query\DateCacheQueryContract,
    Gateways\Internal\Cache\CacheGatewayContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final class DateCacheQueryService implements DateCacheQueryContract
{
    private const CACHE_POOL = 'current_date_cache';
    private const CACHE_KEY = 'current_date_prefix';

    private CacheProxyContract $cache;

    /**
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(
        CacheGatewayContract $cacheGateway,
    ) {
        $this->cache = $cacheGateway->getCache(self::CACHE_POOL);
    }

    /**
     * @return DateCacheObject
     *
     * @throws \LogicException
    */
    public function getCurrentData(): DateCacheObject
    {
        $cacheKey = self::CACHE_KEY;

        $data = $this->cache->get(
            $cacheKey,
            fn (CacheItemProxyContract $item) => $this->fetchAllDates($item),
        );

        return CacheAssertion::assertValidType($data, DateCacheObject::class);
    }

    /**
     * @param CacheItemProxyContract $item
     *
     * @return DateCacheObject
    */
    private function fetchAllDates(CacheItemProxyContract $item): DateCacheObject
    {
        $this->configureCacheItem($item);

        return DateCacheObject::fromDate(new \DateTime());
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
}
