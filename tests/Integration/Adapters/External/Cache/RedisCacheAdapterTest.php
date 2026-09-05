<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Cache;

use PHPUnit\Framework\TestCase;

use Predis\Client as PredisClient;

use App\Core\Ports\{
    Gateways\External\Cache\RedisCacheGatewayContract,
    Shared\Proxy\CacheProxyContract
};

use App\Adapters\External\Cache\RedisCacheAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Cache\RedisCacheAdapter
*/
final class RedisCacheAdapterTest extends TestCase
{
    private const REDIS_URL = 'redis://localhost:6379';

    private RedisCacheAdapter $adapter;

    protected function setUp(): void
    {
        $this->initAdapter();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(RedisCacheGatewayContract::class, $this->adapter);
    }

    public function testIsRedisEnabledReturnsTrueWhenEnabled(): void
    {
        $adapter = new RedisCacheAdapter(true, self::REDIS_URL);

        $this->assertTrue($adapter->isRedisEnabled());
    }

    public function testIsRedisEnabledReturnsFalseWhenDisabled(): void
    {
        $adapter = new RedisCacheAdapter(false, self::REDIS_URL);

        $this->assertFalse($adapter->isRedisEnabled());
    }

    public function testCreateRedisCacheReturnsCacheProxyContract(): void
    {
        $result = $this->adapter->createRedisCache('test_namespace');

        $this->assertInstanceOf(CacheProxyContract::class, $result);
    }

    public function testCreateRedisCacheReturnsDifferentInstancesPerNamespace(): void
    {
        $cache1 = $this->adapter->createRedisCache('namespace_a');
        $cache2 = $this->adapter->createRedisCache('namespace_b');

        $this->assertNotSame($cache1, $cache2);
    }

    public function testClientIsLazilyInitialized(): void
    {
        $adapter = new RedisCacheAdapter(true, self::REDIS_URL);

        $clientBefore = $this->getClientProperty($adapter);

        $this->assertNull($clientBefore);

        $adapter->createRedisCache('test');

        $clientAfter = $this->getClientProperty($adapter);

        $this->assertInstanceOf(PredisClient::class, $clientAfter);
    }

    public function testClientIsSingletonAcrossMultipleCalls(): void
    {
        $this->adapter->createRedisCache('namespace_a');

        $client1 = $this->getClientProperty($this->adapter);

        $this->adapter->createRedisCache('namespace_b');

        $client2 = $this->getClientProperty($this->adapter);

        $this->assertSame($client1, $client2);
    }

    private function initAdapter(): void
    {
        $this->adapter = new RedisCacheAdapter(true, self::REDIS_URL);
    }

    private function getClientProperty(RedisCacheAdapter $adapter): ?PredisClient
    {
        $property = new \ReflectionProperty($adapter, 'client');

        $value = $property->getValue($adapter);

        return $value instanceof PredisClient ? $value : null;
    }
}
