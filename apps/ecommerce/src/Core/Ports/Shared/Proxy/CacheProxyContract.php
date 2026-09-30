<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Proxy;

interface CacheProxyContract
{
    /**
     * @param string $key
     * @param callable(CacheItemProxyContract): mixed $callback
     *
     * @return mixed
    */
    public function get(string $key, callable $callback): mixed;

    /**
     * @param string $key
     *
     * @return bool
    */
    public function delete(string $key): bool;
}
