<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Proxy;

use Symfony\Contracts\Cache\ItemInterface;

use App\Core\Ports\Shared\Proxy\CacheItemProxyContract;

final readonly class CacheItemProxy implements CacheItemProxyContract
{
    public function __construct(
        private ItemInterface $item,
    ) {}

    public function set(mixed $value): void
    {
        $this->item->set($value);
    }

    public function expiresAfter(int $seconds): void
    {
        $this->item->expiresAfter($seconds);
    }
}
