<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Password;

use App\Core\Domain\{
    Segment\Password\Entity\PasswordResetToken,
    Segment\User\Entity\User
};

use App\Core\Ports\Shared\Repository\CleanableTokenRepositoryContract;

interface PasswordResetTokenRepositoryContract extends CleanableTokenRepositoryContract
{
    /**
     * @param string $token
     *
     * @return PasswordResetToken|null
    */
    public function findByToken(string $token): ?PasswordResetToken;

    /**
     * @param User $user
     *
     * @return void
    */
    public function removeTokensByUser(User $user): void;
}
