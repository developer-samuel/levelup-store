<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Proxy;

interface CacheItemProxyContract
{
    public function set(mixed $value): void;
    public function expiresAfter(int $seconds): void;
}
