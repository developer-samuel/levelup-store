<?php

declare(strict_types=1);

namespace App\Adapters\Internal\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use App\Core\Ports\{
    Gateways\External\Cache\RedisCacheGatewayContract,
    Gateways\Internal\Cache\CacheGatewayContract,
    Shared\Proxy\CacheProxyContract
};

use App\Infrastructure\Shared\Proxy\CacheProxy;

final readonly class CacheAdapter implements CacheGatewayContract
{
    /**
     * @param RedisCacheGatewayContract $redis
    */
    public function __construct(
        private RedisCacheGatewayContract $redis,
    ) {}

    /**
     * @param string $namespace
     *
     * @return CacheProxyContract
    */
    public function getCache(string $namespace = ''): CacheProxyContract
    {
        if ($this->redis->isRedisEnabled()) {
            return $this->redis->createRedisCache($namespace);
        }

        return $this->createFilesystemCache($namespace);
    }

    /**
     * @param string $namespace
     *
     * @return CacheProxyContract
    */
    public function createFilesystemCache(string $namespace): CacheProxyContract
    {
        $filesystem = new FilesystemAdapter(namespace: $namespace);

        return new CacheProxy($filesystem);
    }
}
