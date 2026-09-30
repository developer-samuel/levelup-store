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
    /**
     * @param User $user
     *
     * @return RefreshToken
    */
    public function create(User $user): RefreshToken;

    /**
     * @param string $token
     *
     * @return RefreshToken|null
    */
    public function findByToken(string $token): ?RefreshToken;

    /**
     * @param RefreshToken $token
     *
     * @return void
    */
    public function revoke(RefreshToken $token): void;

    /**
     * @param User $user
     *
     * @return void
    */
    public function removeTokensByUser(User $user): void;
}
