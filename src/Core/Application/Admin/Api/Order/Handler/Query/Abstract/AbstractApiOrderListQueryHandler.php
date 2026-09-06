<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Order\Handler\Query\Abstract;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderStatus
};

use App\Core\Application\Admin\Abstract\AbstractAdminApiListQueryHandler;

use App\Core\Ports\{
    Segment\Order\Repository\OrderRepositoryContract,
    Shared\Logging\AppLoggerContract
};

abstract class AbstractApiOrderListQueryHandler extends AbstractAdminApiListQueryHandler
{
    /**
     * @param OrderRepositoryContract $orderRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        protected readonly OrderRepositoryContract $orderRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return OrderStatus[]
    */
    abstract protected function getFilterStatuses(): array;

    /**
     * @param string[] $statuses
     *
     * @return OrderStatus[]
    */
    protected function mapStatuses(array $statuses): array
    {
        return array_map(
            static fn(string $status): OrderStatus => OrderStatus::from($status),
            $statuses,
        );
    }

    /**
     * @return array<int, Order>
    */
    protected function getRepositoryClass(array $context = []): array
    {
        return array_values(
            $this->orderRepository->findOrdersByStatuses($this->getFilterStatuses()),
        );
    }
}
