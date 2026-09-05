<?php

declare(strict_types=1);

namespace App\Core\Application\Cache\Service\Query;

use App\Core\Domain\Cache\CoreCacheObject;

use App\Core\Application\{
    Shared\Constants\CacheTTLConstants
};

use App\Core\Ports\{
    Cache\Service\Query\CoreCacheQueryContract,
    Gateways\Internal\Cache\CacheGatewayContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

use App\Shared\Constants\PathConstants;

final class CoreCacheQueryService implements CoreCacheQueryContract
{
    private const CACHE_POOL = 'base_view_cache';
    private const CACHE_KEY_PREFIX = 'base_view_prefix_';

    private CacheProxyContract $cache;

    /**
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(CacheGatewayContract $cacheGateway)
    {
        $this->cache = $cacheGateway->getCache(self::CACHE_POOL);
    }

    /**
     * @param string $path
     *
     * @return CoreCacheObject
    */
    public function getVars(string $path): CoreCacheObject
    {
        $cacheKey = $this->getCacheKey($path);

        return $this->fetchCachedData($cacheKey, $path);
    }

    /**
     * @param string $path
     *
     * @return string
    */
    private function getCacheKey(string $path): string
    {
        return self::CACHE_KEY_PREFIX . md5($path);
    }

    /**
     * @param string $cacheKey
     * @param string $path
     *
     * @return CoreCacheObject
    */
    private function fetchCachedData(string $cacheKey, string $path): CoreCacheObject
    {
        /** @var CoreCacheObject $data */
        $data = $this->cache->get(
            $cacheKey,
            fn(CacheItemProxyContract $item): CoreCacheObject =>
                $this->buildCacheItem($item, $path),
        );

        return $data;
    }

    /**
     * @param CacheItemProxyContract $item
     * @param string $path
     *
     * @return CoreCacheObject
    */
    private function buildCacheItem(CacheItemProxyContract $item, string $path): CoreCacheObject
    {
        $item->expiresAfter(CacheTTLConstants::ONE_DAY);

        $guestPaths = $this->getGuestPaths($path);
        $adminPaths = $this->resolveAdminPaths($path);

        $isAdminPath = $this->isAdminPath($path);

        $showHeader = $this->shouldShowHeader($path);
        $showFooter = $this->shouldShowFooter($path);

        return new CoreCacheObject(
            $path,
            $guestPaths,
            $adminPaths,
            $isAdminPath,
            $showHeader,
            $showFooter,
        );
    }

    /**
     * @param string $path
     *
     * @return array<int, string>
    */
    private function getGuestPaths(string $path): array
    {
        return array_filter(
            PathConstants::GUEST_PATHS,
            static fn(string $guestPath): bool => str_starts_with($path, $guestPath),
        );
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function isAdminPath(string $path): bool
    {
        return $path === PathConstants::ADMIN_BASE_PATH;
    }

    /**
     * @param string $path
     *
     * @return array<int, string>
    */
    private function resolveAdminPaths(string $path): array
    {
        return str_starts_with($path, PathConstants::ADMIN_BASE_PATH)
            ? [PathConstants::ADMIN_BASE_PATH]
            : [];
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function shouldShowHeader(string $path): bool
    {
        return !$this->isGuestPath($path)
            && !$this->isMustVerifyPath($path);
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function shouldShowFooter(string $path): bool {
        return !$this->isGuestPath($path) && !$this->isMustVerifyPath($path);
    }

    /**
     *
     * @param string $path
     *
     * @return bool
    */
    private function isGuestPath(string $path): bool
    {
        return !empty($this->getGuestPaths($path));
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function isMustVerifyPath(string $path): bool
    {
        return str_starts_with($path, PathConstants::VERIFY_BASE_PATH);
    }
}
