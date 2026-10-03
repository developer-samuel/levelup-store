<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Cache;

use App\Core\Ports\Shared\Proxy\CacheProxyContract;

interface CacheGatewayContract
{
    public function getCache(string $namespace = ''): CacheProxyContract;
}
