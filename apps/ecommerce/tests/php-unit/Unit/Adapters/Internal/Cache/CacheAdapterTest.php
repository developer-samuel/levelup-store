<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters\Internal\Cache;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Ports\{
    Gateways\External\Cache\RedisCacheGatewayContract,
    Shared\Proxy\CacheProxyContract
};

use App\Adapters\Internal\Cache\CacheAdapter;

/**
 * @coversDefaultClass \App\Adapters\Internal\Cache\CacheAdapter
*/
final class CacheAdapterTest extends TestCase
{
    private RedisCacheGatewayContract&MockObject $redis;
    private CacheAdapter $adapter;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initAdapter();
    }

    public function testGetCacheReturnsRedisCacheWhenRedisEnabled(): void
    {
        $redisCache = $this->createMock(CacheProxyContract::class);

        $this->enableRedis();

        $this->redis
            ->expects(self::once())
            ->method('createRedisCache')
            ->with('orders')
            ->willReturn($redisCache);

        $result = $this->adapter->getCache('orders');

        self::assertSame($redisCache, $result);
    }

    public function testGetCacheReturnsDifferentInstancesPerNamespace(): void
    {
        $this->disableRedis();

        $cache1 = $this->adapter->getCache('namespace_a');
        $cache2 = $this->adapter->getCache('namespace_b');

        self::assertNotSame($cache1, $cache2);
    }

    private function initMocks(): void
    {
        $this->redis = $this->createMock(RedisCacheGatewayContract::class);
    }

    private function initAdapter(): void
    {
        $this->adapter = new CacheAdapter($this->redis);
    }

    private function enableRedis(): void
    {
        $this->redis->method('isRedisEnabled')->willReturn(true);
    }

    private function disableRedis(): void
    {
        $this->redis->method('isRedisEnabled')->willReturn(false);
    }
}
