<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Handler\Query;

use App\Core\Domain\Segment\Order\Entity\Order;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Order\Handler\Query\GetOrderListQueryHandlerContract,
    Segment\Order\Service\Query\OrderCacheQueryContract
};

final readonly class GetOrderListQueryHandler implements GetOrderListQueryHandlerContract
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param OrderCacheQueryContract $orderCacheQuery
    */
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private OrderCacheQueryContract $orderCacheQuery,
    ) {}

    /**
     * @return Order[]
    */
    public function handle(): array
    {
        $user = $this->securityProvider->getCurrentUser();

        return $this->orderCacheQuery->getOrders($user);
    }
}
