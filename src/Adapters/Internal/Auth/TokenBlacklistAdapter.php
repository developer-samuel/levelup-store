<?php

declare(strict_types=1);

namespace App\Adapters\Internal\Auth;

use App\Core\Ports\{
    Gateways\External\Cache\RedisCacheGatewayContract,
    Gateways\Internal\Auth\TokenBlacklistContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final class TokenBlacklistAdapter implements TokenBlacklistContract
{
    private const NAMESPACE = 'token_blacklist';
    private const MISS_TTL  = 5; // seconds

    private readonly ?CacheProxyContract $cache;

    /**
     * @param RedisCacheGatewayContract $redis
    */
    public function __construct(
        RedisCacheGatewayContract $redis
    ) {
        $this->cache = $redis->isRedisEnabled()
            ? $redis->createRedisCache(self::NAMESPACE)
            : null;
    }

    /**
     * @param string $token
     * @param \DateTimeImmutable $expiresAt
     *
     * @return void
    */
    public function blacklist(string $token, \DateTimeImmutable $expiresAt): void
    {
        if ($this->cache === null) {
            return;
        }

        $ttl  = max(1, $expiresAt->getTimestamp() - time());
        $hash = $this->hash($token);

        $this->cache->delete($hash);

        $this->cache->get($hash, function (CacheItemProxyContract $item) use ($ttl): int {
            $item->expiresAfter($ttl);
            return 1;
        });
    }

    /**
     * @param string $token
     *
     * @return bool
    */
    public function isBlacklisted(string $token): bool
    {
        if ($this->cache === null) {
            return false;
        }

        $hash   = $this->hash($token);
        $result = $this->cache->get($hash, function (CacheItemProxyContract $item): int {
            $item->expiresAfter(self::MISS_TTL);
            return 0;
        });

        return $result === 1;
    }

    /**
     * @param string $token
     *
     * @return string
    */
    private function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
