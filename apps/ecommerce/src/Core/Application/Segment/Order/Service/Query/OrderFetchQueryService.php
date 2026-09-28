<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Service\Query;

use Kit\Assertion\Domain\Order\OrderAssertion;

use App\Core\Domain\Segment\Order\Entity\Order;

use App\Core\Ports\{
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\Order\Service\Query\OrderFetchQueryContract
};

final class OrderFetchQueryService implements OrderFetchQueryContract
{
    /**
     * @param OrderRepositoryContract $orderRepository
    */
    public function __construct(
        private OrderRepositoryContract $orderRepository,
    ) {}

    /**
     * @param string $code
     *
     * @return Order
     *
     * @throws \InvalidArgumentException
    */
    public function getOrderByCodeOrFail(string $code): Order
    {
        $order = $this->orderRepository->getOrderByCode($code);
        OrderAssertion::assertExists($order);

        return $order;
    }
}
