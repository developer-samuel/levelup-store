<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Repository;

use App\Core\Domain\Segment\Cart\Entity\Cart;

interface CartRepositoryContract
{
    /**
     * @param int $userId
     *
     * @return Cart|null
    */
    public function findCartForUser(int $userId): ?Cart;

    /**
     * @param \DateTimeImmutable $threshold
     *
     * @return Cart[]
    */
    public function findInactiveSince(\DateTimeImmutable $threshold): array;

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return Cart[]
    */
    public function findAbandonedForReminder(\DateTimeImmutable $from, \DateTimeImmutable $to): array;

    /**
     * @return Cart[]
    */
    public function findEmpty(): array;
}
