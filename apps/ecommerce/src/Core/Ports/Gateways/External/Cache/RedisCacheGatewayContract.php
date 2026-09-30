<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Cache;

use App\Core\Ports\Shared\Proxy\CacheProxyContract;

interface RedisCacheGatewayContract
{
    /**
     * @return bool
    */
    public function isRedisEnabled(): bool;

    /**
     * @param string $namespace
     *
     * @return CacheProxyContract
    */
    public function createRedisCache(string $namespace): CacheProxyContract;
}
