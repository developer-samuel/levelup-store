<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\Internal\Auth;

use PHPUnit\Framework\TestCase;

use App\Adapters\{
    External\Cache\RedisCacheAdapter,
    Internal\Auth\TokenBlacklistAdapter
};

use App\Core\Ports\Gateways\Internal\Auth\TokenBlacklistContract;

/**
 * @coversDefaultClass \App\Adapters\Internal\Auth\TokenBlacklistAdapter
*/
final class TokenBlacklistAdapterTest extends TestCase
{
    private TokenBlacklistAdapter $adapter;

    protected function setUp(): void
    {
        $this->initAdapter();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(TokenBlacklistContract::class, $this->adapter);
    }

    public function testIsNotBlacklistedByDefault(): void
    {
        $this->assertFalse($this->adapter->isBlacklisted('some-token'));
    }

    public function testBlacklistMakesTokenBlacklisted(): void
    {
        $token     = $this->uniqueToken();
        $expiresAt = new \DateTimeImmutable('+30 days');

        $this->adapter->blacklist($token, $expiresAt);

        $this->assertTrue($this->adapter->isBlacklisted($token));
    }

    public function testBlacklistDoesNotAffectOtherTokens(): void
    {
        $tokenA = $this->uniqueToken();
        $tokenB = $this->uniqueToken();

        $this->adapter->blacklist($tokenA, new \DateTimeImmutable('+30 days'));

        $this->assertFalse($this->adapter->isBlacklisted($tokenB));
    }

    public function testTokenIsNoLongerBlacklistedAfterExpiry(): void
    {
        $token     = $this->uniqueToken();
        $expiresAt = new \DateTimeImmutable('+1 second');

        $this->adapter->blacklist($token, $expiresAt);

        $this->assertTrue($this->adapter->isBlacklisted($token));

        sleep(2);

        $this->assertFalse($this->adapter->isBlacklisted($token));
    }

    public function testBlacklistOverwritesCachedMissEntry(): void
    {
        $token     = $this->uniqueToken();
        $expiresAt = new \DateTimeImmutable('+30 days');

        $this->assertFalse($this->adapter->isBlacklisted($token));

        $this->adapter->blacklist($token, $expiresAt);

        $this->assertTrue($this->adapter->isBlacklisted($token));
    }

    public function testNoopWhenRedisDisabled(): void
    {
        $redis   = new RedisCacheAdapter(false, $this->redisUrl());
        $adapter = new TokenBlacklistAdapter($redis);

        $token = $this->uniqueToken();

        $adapter->blacklist($token, new \DateTimeImmutable('+30 days'));

        $this->assertFalse($adapter->isBlacklisted($token));
    }

    private function initAdapter(): void
    {
        $redis         = new RedisCacheAdapter(true, $this->redisUrl());
        $this->adapter = new TokenBlacklistAdapter($redis);
    }

    private function redisUrl(): string
    {
        $url = $_SERVER['REDIS_URL'] ?? 'redis://localhost:6379';

        return is_string($url) ? $url : 'redis://localhost:6379';
    }

    private function uniqueToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
