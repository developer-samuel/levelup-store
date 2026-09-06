<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Country\Service;

use App\Core\Domain\Segment\Country\Entity\Country;

use App\Core\Application\Shared\Constants\CacheTTLConstants;

use App\Core\Ports\{
    Gateways\Internal\Cache\CacheGatewayContract,
    Segment\Country\CountryRepositoryContract,
    Segment\Country\Service\CountryCacheQueryContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final class CountryCacheQueryService implements CountryCacheQueryContract
{
    private const CACHE_POOL = 'country_list_cache';
    private const CACHE_KEY = 'countries_list_prefix';

    private CacheProxyContract $cache;

    /**
     * @param CountryRepositoryContract $countryRepository
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(
        private readonly CountryRepositoryContract $countryRepository,
        CacheGatewayContract $cacheGateway,
    ) {
        $this->cache = $cacheGateway->getCache(self::CACHE_POOL);
    }

    /**
     * @return Country[]
    */
    public function getAllCountries(): array
    {
        $cacheKey = $this->getCacheKey();

        $data = $this->fetchCachedData($cacheKey);

        return array_values($data);
    }

    /**
     * @return string
    */
    private function getCacheKey(): string
    {
        return self::CACHE_KEY;
    }

    /**
     * @param string $cacheKey
     *
     * @return Country[]
    */
    private function fetchCachedData(string $cacheKey): array
    {
        /** @var Country[] $data */
        $data = $this->cache->get(
            $cacheKey,
            fn (CacheItemProxyContract $item): array => $this->fetchCountries($item),
        );

        return $data;
    }

    /**
     * @param CacheItemProxyContract $item
     *
     * @return Country[]
    */
    private function fetchCountries(CacheItemProxyContract $item): array
    {
        $this->configureCacheItem($item);

        return $this->countryRepository->findAll();
    }

    /**
     * @param CacheItemProxyContract $item
     *
     * @return void
    */
    private function configureCacheItem(CacheItemProxyContract $item): void
    {
        $item->expiresAfter(CacheTTLConstants::HALF_YEAR);
    }
}
