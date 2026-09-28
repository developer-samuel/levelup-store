<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Builder;

use App\Core\Ports\{
    Segment\Order\Service\Command\OrderCacheCommandContract,
    Segment\Order\Service\Command\OrderDataCommandContract,
    Segment\Order\Service\Command\OrderItemCommandContract,
    Segment\Order\Service\Command\OrderPreparationCommandContract
};

final readonly class OrderCommandBuilder
{
    /**
     * @param OrderDataCommandContract $orderDataCommand
     * @param OrderPreparationCommandContract $orderPreparationCommand
     * @param OrderItemCommandContract $orderItemCommand
     * @param OrderCacheCommandContract $orderCacheCommand
    */
    public function __construct(
        public OrderDataCommandContract $orderDataCommand,
        public OrderPreparationCommandContract $orderPreparationCommand,
        public OrderItemCommandContract $orderItemCommand,
        public OrderCacheCommandContract $orderCacheCommand,
    ) {}
}
