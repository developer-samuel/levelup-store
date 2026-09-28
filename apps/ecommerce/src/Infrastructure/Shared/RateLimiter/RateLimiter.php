<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\RateLimiter;

use Psr\{
    Cache\CacheItemInterface,
    Cache\CacheItemPoolInterface
};

use Symfony\{
    Component\DependencyInjection\Attribute\Autoconfigure,
    Component\HttpFoundation\RequestStack
};

use App\Core\Domain\Shared\Exception\TooManyRequestsException;

use App\Core\Ports\Shared\RateLimiter\RateLimiterContract;

use App\Infrastructure\Shared\Http\RequestMetadata;

#[Autoconfigure(autowire: false)]
final readonly class RateLimiter implements RateLimiterContract
{
    private const LOCKOUT_THRESHOLDS = [
        25 => 1800, // 30min
        20 => 900,  // 15min
        15 => 600,  // 10min
        10 => 300,  // 5min
        5  => 60,   // 60s
    ];

    /**
     * @param CacheItemPoolInterface $cache
     * @param RequestStack $requestStack
     * @param string $keyPrefix
     * @param int $recordTtl
    */
    public function __construct(
        private CacheItemPoolInterface $cache,
        private RequestStack $requestStack,
        private string $keyPrefix,
        private int $recordTtl = 86400,
    ) {}

    /**
     * @return void
     *
     * @throws TooManyRequestsException
    */
    public function track(): void
    {
        $item = $this->cache->getItem($this->buildKey());
        $data = $this->resolveData($item);

        if ($data['locked_until'] !== null && time() < $data['locked_until']) {
            throw new TooManyRequestsException($data['locked_until'] - time());
        }

        $data['locked_until'] = null;
        $data['attempts'] = (int) $data['attempts'] + 1;

        $lockoutSeconds = $this->resolveLockout($data['attempts']);

        if ($lockoutSeconds > 0) {
            $data['locked_until'] = time() + $lockoutSeconds;
            $this->saveItem($item, $data);

            throw new TooManyRequestsException($lockoutSeconds);
        }

        $this->saveItem($item, $data);
    }

    /**
     * @return void
    */
    public function reset(): void
    {
        $this->cache->deleteItem($this->buildKey());
    }

    /**
     * @param CacheItemInterface $item
     *
     * @return array<string, int|null>
    */
    private function resolveData(CacheItemInterface $item): array
    {
        /** @var array<string, int|null> $data */
        $data = $item->isHit() ? $item->get() : ['attempts' => 0, 'locked_until' => null];

        return $data;
    }

    /**
     * @param int $attempts
     *
     * @return int
    */
    private function resolveLockout(int $attempts): int
    {
        if (isset(self::LOCKOUT_THRESHOLDS[$attempts])) {
            return self::LOCKOUT_THRESHOLDS[$attempts];
        }

        $maxThreshold = array_key_first(self::LOCKOUT_THRESHOLDS);
        $step = array_key_last(self::LOCKOUT_THRESHOLDS);

        if ($attempts > $maxThreshold && $attempts % $step === 0) {
            return self::LOCKOUT_THRESHOLDS[$maxThreshold];
        }

        return 0;
    }

    /**
     * @param CacheItemInterface $item
     * @param array<string, int|null> $data
     *
     * @return void
    */
    private function saveItem(CacheItemInterface $item, array $data): void
    {
        $item->set($data);
        $item->expiresAfter($this->recordTtl);

        $this->cache->save($item);
    }

    /**
     * @return string
    */
    private function buildKey(): string
    {
        $ip = RequestMetadata::fromRequestStack($this->requestStack)->ip;

        return sprintf('rate_limit_%s_%s', $this->keyPrefix, md5($ip));
    }
}
