<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Proxy;

interface CacheProxyContract
{
    /** @param callable(CacheItemProxyContract): mixed $callback */
    public function get(string $key, callable $callback): mixed;

    public function delete(string $key): bool;
}
