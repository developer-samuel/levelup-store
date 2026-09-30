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
    /**
     * @param string|null $token
     *
     * @return User
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundException
    */
    public function getValidUserWithToken(?string $token): User;

    /**
     * @param string|null $token
     *
     * @return PasswordResetToken|null
    */
    public function getValidToken(?string $token): ?PasswordResetToken;
}
