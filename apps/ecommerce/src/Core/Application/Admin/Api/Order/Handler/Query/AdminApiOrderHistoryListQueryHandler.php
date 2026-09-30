<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\Order\Handler\Query;

use App\Core\Domain\Segment\Order\Enum\OrderStatus;

use App\Core\Application\{
    Admin\Api\Order\Handler\Query\Abstract\AbstractApiOrderListQueryHandler,
    Admin\Api\Order\Resource\AdminApiOrderResource
};

use App\Core\Ports\{
    Segment\Order\Repository\OrderRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final class AdminApiOrderHistoryListQueryHandler extends AbstractApiOrderListQueryHandler
{
    /**
     * @param OrderRepositoryContract $orderRepository
     * @param AppLoggerContract $logger
    */
    public function __construct(
        OrderRepositoryContract $orderRepository,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $orderRepository,
            $logger,
        );
    }

    /**
     * @return OrderStatus[]
    */
    protected function getFilterStatuses(): array
    {
        return $this->mapStatuses(OrderStatus::completedStatuses());
    }

    /**
     * @return string
    */
    protected function getResourceClass(): string
    {
        return AdminApiOrderResource::class;
    }
}
