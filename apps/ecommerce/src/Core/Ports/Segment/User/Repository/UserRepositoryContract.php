<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Repository;

use App\Core\Domain\Segment\User\Entity\User;

interface UserRepositoryContract
{
    /** @return User[] */
    public function findAll(): array;

    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function countUsersBetween(\DateTimeImmutable $from, \DateTimeImmutable $to): int;
}
