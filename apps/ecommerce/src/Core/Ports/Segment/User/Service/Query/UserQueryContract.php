<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Query;

use App\Core\Domain\Segment\User\Entity\User;

interface UserQueryContract
{
    public function findUserByEmailOrFail(string $email): User;
    public function isAdmin(User $user): bool;
}
