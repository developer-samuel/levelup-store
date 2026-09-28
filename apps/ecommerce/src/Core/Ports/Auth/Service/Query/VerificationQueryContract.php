<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Entity\UserVerificationToken
};

interface VerificationQueryContract
{
    /**
     * @param string $token
     *
     * @return UserVerificationToken|null
    */
    public function getValidToken(string $token): ?UserVerificationToken;

    /**
     * @param User|null $user
     *
     * @return bool
    */
    public function isUserVerifiable(?User $user): bool;
}
