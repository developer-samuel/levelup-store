<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Segment\Dashboard\Service;

use App\Core\Domain\Shared\ValueObject\DateIntervalObject;

use App\Core\Ports\{
    Admin\Segment\Dashboard\Service\AdminDashboardQueryContract,
    Segment\Order\Repository\OrderRepositoryContract,
    Segment\User\Repository\UserRepositoryContract
};

final readonly class AdminDashboardQueryService implements AdminDashboardQueryContract
{
    private \DateTimeImmutable $now;

    /**
     * @param OrderRepositoryContract $orderRepository
     * @param UserRepositoryContract $userRepository
    */
    public function __construct(
        private OrderRepositoryContract $orderRepository,
        private UserRepositoryContract $userRepository,
    ) {
        $this->now = new \DateTimeImmutable();
    }

    /**
     * @return int[]
    */
    public function getOrdersPerDayCurrentMonth(): array
    {
        return $this->countPerDay(
            $this->generateDailyIntervalsCurrentMonth(),
            fn(DateIntervalObject $interval): int => $this->orderRepository->countOrdersBetween($interval->start, $interval->end),
        );
    }

    /**
     * @return int[]
    */
    public function getOrdersPaidUnpaidCurrentMonth(): array
    {
        $interval = $this->getCurrentMonthInterval();

        return [
            $this->orderRepository->countPaidOrdersBetween($interval->start, $interval->end),
            $this->orderRepository->countUnpaidOrdersBetween($interval->start, $interval->end),
        ];
    }

    /**
     * @return int[]
    */
    public function getUsersCountLast7Days(): array
    {
        return $this->countPerDay(
            $this->generateLastNDaysIntervals(7),
            fn(DateIntervalObject $interval): int => $this->userRepository->countUsersBetween($interval->start, $interval->end),
        );
    }

    /**
     * @return array<int, DateIntervalObject>
    */
    private function generateDailyIntervalsCurrentMonth(): array
    {
        $startDay = $this->now->modify('first day of this month')->setTime(0, 0, 0);
        $daysInMonth = (int) $this->now->format('t');

        return $this->generateDailyIntervals($daysInMonth, $startDay);
    }

    /**
     * @param int $days
     *
     * @return array<int, DateIntervalObject>
    */
    private function generateLastNDaysIntervals(int $days): array
    {
        $startDay = $this->now->modify(sprintf('-%d days', $days - 1))->setTime(0, 0, 0);

        return $this->generateDailyIntervals($days, $startDay);
    }

    /**
     * @param int $days
     * @param \DateTimeImmutable $startDay
     *
     * @return array<int, DateIntervalObject>
    */
    private function generateDailyIntervals(int $days, \DateTimeImmutable $startDay): array
    {
        $intervals = [];

        for ($i = 0; $i < $days; $i++) {
            $start = $startDay->add(new \DateInterval(sprintf('P%dD', $i)));
            $end = $start->add(new \DateInterval('P1D'));
            $intervals[] = new DateIntervalObject($start, $end);
        }

        return $intervals;
    }

    /**
     * @return DateIntervalObject
    */
    private function getCurrentMonthInterval(): DateIntervalObject
    {
        $start = $this->now->modify('first day of this month')->setTime(0, 0, 0);
        $end = $this->now->modify('first day of next month')->modify('-1 second');

        return new DateIntervalObject($start, $end);
    }

    /**
     * @template T
     *
     * @param array<int, DateIntervalObject> $intervals
     * @param callable(DateIntervalObject): T $callback
     *
     * @return array<int, T>
    */
    private function countPerDay(array $intervals, callable $callback): array
    {
        return array_map(
            static fn (DateIntervalObject $interval) => $callback($interval),
            $intervals,
        );
    }
}
