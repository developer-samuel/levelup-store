<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

interface UserQueryContract
{
    /**
     * @param string $email
     *
     * @return User
     *
     * @throws \InvalidArgumentException
    */
    public function findUserByEmailOrFail(string $email): User;

    /**
     * @param User $user
     *
     * @return bool
    */
    public function isAdmin(User $user): bool;
}
