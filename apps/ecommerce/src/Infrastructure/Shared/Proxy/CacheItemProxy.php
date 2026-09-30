<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Proxy;

use Symfony\Contracts\Cache\ItemInterface;

use App\Core\Ports\Shared\Proxy\CacheItemProxyContract;

final readonly class CacheItemProxy implements CacheItemProxyContract
{
    /**
     * @param ItemInterface $item
    */
    public function __construct(
        private ItemInterface $item,
    ) {}

    /**
     * @param mixed $value
     *
     * @return void
    */
    public function set(mixed $value): void
    {
        $this->item->set($value);
    }

    /**
     * @param int $seconds
     *
     * @return void
    */
    public function expiresAfter(int $seconds): void
    {
        $this->item->expiresAfter($seconds);
    }
}
