<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Proxy;

interface CacheItemProxyContract
{
    /**
     * @param mixed $value
     *
     * @return void
    */
    public function set(mixed $value): void;

    /**
     * @param int $seconds
     *
     * @return void
    */
    public function expiresAfter(int $seconds): void;
}
