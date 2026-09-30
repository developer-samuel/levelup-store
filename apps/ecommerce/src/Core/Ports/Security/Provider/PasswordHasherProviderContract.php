<?php

declare(strict_types=1);

namespace App\Core\Ports\Security\Provider;

use App\Core\Domain\Segment\User\Entity\User;

interface PasswordHasherProviderContract
{
    /**
     * @param User $user
     * @param string $password
    */
    public function hash(User $user, string $password): string;

    /**
     * @param User $user
     * @param string $password
     *
     * @return bool
    */
    public function isPasswordValid(User $user, string $password): bool;
}
