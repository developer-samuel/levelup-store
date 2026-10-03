<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Cache;

use App\Core\Ports\Shared\Proxy\CacheProxyContract;

interface RedisCacheGatewayContract
{
    public function isRedisEnabled(): bool;
    public function createRedisCache(string $namespace): CacheProxyContract;
}
