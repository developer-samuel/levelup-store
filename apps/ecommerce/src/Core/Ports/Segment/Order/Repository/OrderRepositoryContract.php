<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Repository;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\Enum\OrderStatus,
    Segment\User\Entity\User
};

interface OrderRepositoryContract
{
    /**
     * @return Order[]
    */
    public function findAll(): array;

    /**
     * @param int $orderId
     *
     * @return Order|null
    */
    public function getOrder(int $orderId): ?Order;

    /**
     * @param string $code
     *
     * @return Order|null
    */
    public function getOrderByCode(string $code): ?Order;

    /**
     * @param array<string, mixed> $criteria
     *
     * @return Order|null
    */
    public function findOne(array $criteria): ?Order;

    /**
     * @param OrderStatus[] $statuses
     *
     * @return Order[]
    */
    public function findOrdersByStatuses(array $statuses): array;

    /**
     * @param User $user
     *
     * @return Order[]
    */
    public function findAllForUser(User $user): array;

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countPaidOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countUnpaidOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;
}
