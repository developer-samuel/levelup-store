<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Repository;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Entity\UserVerificationToken
};

use App\Core\Ports\Shared\Repository\CleanableTokenRepositoryContract;

interface UserVerificationTokenRepositoryContract extends CleanableTokenRepositoryContract
{
    public function findByToken(string $token): ?UserVerificationToken;
    public function removeTokensByUser(User $user): void;
}
