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
    /** @return Order[] */
    public function findAll(): array;

    public function getOrder(int $orderId): ?Order;
    public function getOrderByCode(string $code): ?Order;

    /** @param array<string, mixed> $criteria */
    public function findOne(array $criteria): ?Order;

    /**
     * @param OrderStatus[] $statuses
     *
     * @return Order[]
    */
    public function findOrdersByStatuses(array $statuses): array;

    /** @return Order[] */
    public function findAllForUser(User $user): array;

    public function countOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;
    public function countPaidOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;
    public function countUnpaidOrdersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;
}
