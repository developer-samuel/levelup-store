<?php

declare(strict_types=1);

namespace App\Adapters\External\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;

use Predis\Client as PredisClient;

use App\Core\Ports\{
    Gateways\External\Cache\RedisCacheGatewayContract,
    Shared\Proxy\CacheProxyContract
};

use App\Infrastructure\Shared\Proxy\CacheProxy;

final class RedisCacheAdapter implements RedisCacheGatewayContract
{
    private ?PredisClient $client = null;

    /**
     * @param bool $redisEnabled
     * @param string $redisUrl
    */
    public function __construct(
        private readonly bool $redisEnabled,
        private readonly string $redisUrl,
    ) {}

    /**
     * @return bool
    */
    public function isRedisEnabled(): bool
    {
        return $this->redisEnabled;
    }

    /**
     * @param string $namespace
     *
     * @return CacheProxyContract
    */
    public function createRedisCache(string $namespace): CacheProxyContract
    {
        $redisAdapter = new RedisAdapter($this->getClient(), namespace: $namespace);

        return new CacheProxy($redisAdapter);
    }

    /**
     * @return PredisClient
    */
    private function getClient(): PredisClient
    {
        if ($this->client === null) {
            $this->client = new PredisClient($this->redisUrl);
        }

        return $this->client;
    }
}
