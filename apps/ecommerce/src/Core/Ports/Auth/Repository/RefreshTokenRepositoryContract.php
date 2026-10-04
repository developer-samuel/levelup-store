<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Repository;

use App\Core\Domain\{
    Auth\Entity\RefreshToken,
    Segment\User\Entity\User
};

use App\Core\Ports\Shared\Repository\CleanableTokenRepositoryContract;

interface RefreshTokenRepositoryContract extends CleanableTokenRepositoryContract
{
    public function create(User $user): RefreshToken;
    public function findByToken(string $token): ?RefreshToken;
    public function revoke(RefreshToken $token): void;
    public function removeTokensByUser(User $user): void;
}
