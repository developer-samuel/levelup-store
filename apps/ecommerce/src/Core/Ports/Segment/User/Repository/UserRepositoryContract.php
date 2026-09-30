<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Repository;

use App\Core\Domain\Segment\User\Entity\User;

interface UserRepositoryContract
{
    /**
     * @return User[]
    */
    public function findAll(): array;

    /**
     * @param int $id
     *
     * @return User|null
    */
    public function findById(int $id): ?User;

    /**
     * @param string $email
     *
     * @return User|null
    */
    public function findByEmail(string $email): ?User;

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return int
    */
    public function countUsersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;
}
