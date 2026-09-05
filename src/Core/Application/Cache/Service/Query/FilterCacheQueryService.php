<?php

declare(strict_types=1);

namespace App\Core\Application\Cache\Service\Query;

use Kit\{
    Assertion\Shared\CacheAssertion,
    Utils\Shared\StringNormalizer
};

use App\Core\Domain\Cache\FilterCacheObject;

use App\Core\Application\Shared\Constants\CacheTTLConstants;

use App\Core\Ports\{
    Cache\Service\Query\FilterCacheQueryContract,
    Gateways\Internal\Cache\CacheGatewayContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final class FilterCacheQueryService implements FilterCacheQueryContract
{
    private const CACHE_POOL = 'filter_cache';
    private const CACHE_KEY_PREFIX = 'filter_prefix_';

    private CacheProxyContract $cache;

    /**
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(CacheGatewayContract $cacheGateway)
    {
        $this->cache = $cacheGateway->getCache(self::CACHE_POOL);
    }

    /**
     * @param string $query
     *
     * @return FilterCacheObject
    */
    public function getVars(string $query): FilterCacheObject
    {
        $cacheKey = $this->getCacheKey($query);

        return $this->fetchCachedData($cacheKey, $query);
    }

    /**
     * @param string $query
     *
     * @return string
    */
    private function getCacheKey(string $query): string
    {
        return self::CACHE_KEY_PREFIX . md5($query);
    }

    /**
     * @param string $cacheKey
     * @param string $query
     *
     * @return FilterCacheObject
     *
     * @throws \LogicException
    */
    private function fetchCachedData(string $cacheKey, string $query): FilterCacheObject
    {
        $data = $this->cache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): FilterCacheObject =>
                $this->cacheCallback($item, $query),
        );

        return CacheAssertion::assertValidType($data, FilterCacheObject::class);
    }

    /**
     * @param CacheItemProxyContract $item
     * @param string $query
     *
     * @return FilterCacheObject
    */
    private function cacheCallback(CacheItemProxyContract $item, string $query): FilterCacheObject
    {
        $this->configureCacheItem($item);

        $params = $this->parseQueryString($query);

        $subtypesActive = $this->normalizeQueryParamToArray($params['subtype'] ?? null);
        $brandsActive = $this->normalizeQueryParamToArray($params['brand'] ?? null);

        $step = 1;

        return new FilterCacheObject(
            subtypesActive: $subtypesActive,
            brandsActive: $brandsActive,
            step: $step,
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
     * @param string $queryString
     *
     * @return array<int|string, mixed>
    */
    private function parseQueryString(string $queryString): array
    {
        parse_str($queryString, $params);

        return $params;
    }

    /**
     * @param mixed $param
     *
     * @return list<string>
    */
    private function normalizeQueryParamToArray(mixed $param): array
    {
        $array = $this->convertToArray($param);
        $strings = $this->filterStrings($array);
        return $this->normalizeStrings($strings);
    }

    /**
     * @param mixed $param
     *
     * @return array<mixed>
    */
    private function convertToArray(mixed $param): array
    {
        if (is_array($param)) {
            return $param;
        }

        if (is_string($param) && $param !== '') {
            return explode(',', $param);
        }

        return [];
    }

    /**
     * @param array<mixed> $array
     *
     * @return list<string>
    */
    private function filterStrings(array $array): array
    {
        return array_values(array_filter($array, 'is_string'));
    }

    /**
     * @param list<string> $strings
     *
     * @return list<string>
    */
    private function normalizeStrings(array $strings): array
    {
        return array_map(
            static fn(string $val): string => StringNormalizer::normalize($val),
            $strings,
        );
    }
}
