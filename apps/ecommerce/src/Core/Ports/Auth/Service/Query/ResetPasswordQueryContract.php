<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Query;

use App\Core\Domain\{
    Shared\Exception\NotFoundException,
    Segment\Password\Entity\PasswordResetToken,
    Segment\User\Entity\User
};

interface ResetPasswordQueryContract
{
    public function getValidUserWithToken(?string $token): User;
    public function getValidToken(?string $token): ?PasswordResetToken;
}
