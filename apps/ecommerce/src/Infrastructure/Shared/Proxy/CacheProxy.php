<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Proxy;

use Symfony\{
    Contracts\Cache\CacheInterface,
    Contracts\Cache\ItemInterface
};

use App\Core\Ports\{
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final readonly class CacheProxy implements CacheProxyContract
{
    /**
     * @param CacheInterface $cache
    */
    public function __construct(
        private CacheInterface $cache,
    ) {}

    /**
     * @param string $key
     * @param callable(CacheItemProxyContract): mixed $callback
     *
     * @return mixed
    */
    public function get(string $key, callable $callback): mixed
    {
        return $this->cache->get($key, function (ItemInterface $item) use ($callback) {
            $proxyItem = new CacheItemProxy($item);

            return $callback($proxyItem);
        });
    }

    /**
     * @param string $key
     *
     * @return bool
    */
    public function delete(string $key): bool
    {
        return $this->cache->delete($key);
    }
}
