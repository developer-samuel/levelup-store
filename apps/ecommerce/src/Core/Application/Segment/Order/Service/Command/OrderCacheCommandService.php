<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Application\{
    Segment\Order\Enum\OrderCacheKeyPrefix,
    Segment\Order\Enum\OrderCachePool
};

use App\Core\Ports\{
    Gateways\Internal\Cache\CacheGatewayContract,
    Segment\Order\Service\Command\OrderCacheCommandContract,
    Shared\Proxy\CacheProxyContract
};

final class OrderCacheCommandService implements OrderCacheCommandContract
{
    private CacheProxyContract $cache;

    /**
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(CacheGatewayContract $cacheGateway) {
        $this->cache = $cacheGateway->getCache(OrderCachePool::ORDERS_USER->value);
    }

    /**
     * @param User $user
     *
     * @return void
    */
    public function invalidateOrdersCache(User $user): void
    {
        $cacheKey = OrderCacheKeyPrefix::ORDERS_USER->value . $user->getId();

        $this->cache->delete($cacheKey);
    }
}
