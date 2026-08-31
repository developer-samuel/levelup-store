<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Dashboard\Handler\Query;

use App\Core\Ports\Admin\Segment\Dashboard\Service\Query\AdminDashboardQueryContract;

final readonly class AdminDashboardQueryHandler
{
    /**
     * @param AdminDashboardQueryContract $adminDashboardQuery
    */
    public function __construct(
        private AdminDashboardQueryContract $adminDashboardQuery,
    ) {}

    /**
     * @return array<string, int[]>
    */
    public function handle(): array
    {
        return [
            'ordersPerDay'     => $this->adminDashboardQuery->getOrdersPerDayCurrentMonth(),
            'ordersPaidUnpaid' => $this->adminDashboardQuery->getOrdersPaidUnpaidCurrentMonth(),
            'usersLast7Days'   => $this->adminDashboardQuery->getUsersCountLast7Days(),
        ];
    }
}
