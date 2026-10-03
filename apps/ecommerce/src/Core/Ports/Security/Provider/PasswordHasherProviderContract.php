<?php

declare(strict_types=1);

namespace App\Core\Ports\Security\Provider;

use App\Core\Domain\Segment\User\Entity\User;

interface PasswordHasherProviderContract
{
    public function hash(User $user, string $password): string;
    public function isPasswordValid(User $user, string $password): bool;
}
