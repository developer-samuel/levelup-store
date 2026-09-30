<?php

declare(strict_types=1);

namespace App\Core\Ports\Admin\Segment\Dashboard\Service;

interface AdminDashboardQueryContract
{
    /**
     * @return int[]
    */
    public function getOrdersPerDayCurrentMonth(): array;

    /**
     * @return int[]
    */
    public function getOrdersPaidUnpaidCurrentMonth(): array;

    /**
     * @return int[]
    */
    public function getUsersCountLast7Days(): array;
}
