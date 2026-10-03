<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface ResetPasswordCommandContract
{
    public function resetPassword(User $user, string $password): void;
}
