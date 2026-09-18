<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\User\Entity\User
};

use App\Core\Application\{
    Segment\Order\Enum\OrderCacheKeyPrefix,
    Segment\Order\Enum\OrderCachePool,
    Shared\Constants\CacheTTLConstants
};

use App\Core\Ports\{
    Gateways\Internal\Cache\CacheGatewayContract,
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\Order\Service\Query\OrderCacheQueryContract,
    Shared\Proxy\CacheItemProxyContract,
    Shared\Proxy\CacheProxyContract
};

final class OrderCacheQueryService implements OrderCacheQueryContract
{
    private CacheProxyContract $cache;

    /**
     * @param OrderRepositoryContract $orderRepository
     * @param CacheGatewayContract $cacheGateway
    */
    public function __construct(
        private readonly OrderRepositoryContract $orderRepository,
        CacheGatewayContract $cacheGateway,
    ) {
        $this->cache = $cacheGateway->getCache(OrderCachePool::ORDERS_USER->value);
    }

    /**
     * @param User|null $user
     *
     * @return Order[]
    */
    public function getOrders(?User $user): array
    {
        $cacheKey = $this->getCacheKey($user);

        $data = $this->fetchCachedData($cacheKey, $user);

        return array_values($data);
    }

    /**
     * @param User|null $user
     *
     * @return string
    */
    private function getCacheKey(?User $user): string
    {
        return OrderCacheKeyPrefix::ORDERS_USER->value . ($user !== null ? $user->getId() : 'guest');
    }

    /**
     * @param string $cacheKey
     * @param User|null $user
     *
     * @return Order[]
    */
    private function fetchCachedData(string $cacheKey, ?User $user): array
    {
        /** @var Order[] $data */
        $data = $this->cache->get(
            $cacheKey,
            fn (CacheItemProxyContract $item): array =>
                $this->cacheCallback($item, $user),
        );

        return $data;
    }

    /**
     * @param CacheItemProxyContract $item
     * @param User|null $user
     *
     * @return Order[]
    */
    private function cacheCallback(CacheItemProxyContract $item, ?User $user): array
    {
        $item->expiresAfter(CacheTTLConstants::ONE_HOUR);

        if ($user === null) {
            return [];
        }

        return $this->orderRepository->findAllForUser($user);
    }
}
