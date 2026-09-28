<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Entity\UserVerificationToken
};

use App\Core\Ports\{
    Auth\Service\Query\VerificationQueryContract,
    Segment\User\Repository\UserVerificationTokenRepositoryContract
};

final readonly class VerificationQueryService implements VerificationQueryContract
{
    /**
     * @param UserVerificationTokenRepositoryContract $tokenRepository
    */
    public function __construct(
        private UserVerificationTokenRepositoryContract $tokenRepository,
    ) {}

    /**
     * @param string $token
     *
     * @return UserVerificationToken|null
    */
    public function getValidToken(string $token): ?UserVerificationToken
    {
        $tokenEntity = $this->tokenRepository->findByToken($token);

        if ($tokenEntity === null || !$this->isTokenValid($tokenEntity)) {
            return null;
        }

        return $tokenEntity;
    }

    /**
     * @param User|null $user
     *
     * @return bool
    */
    public function isUserVerifiable(?User $user): bool
    {
        return $user !== null && $user->getEmailVerifiedAt() === null;
    }

    /**
     * @param UserVerificationToken $token
     *
     * @return bool
    */
    private function isTokenValid(UserVerificationToken $token): bool
    {
        return $token->getExpiresAt() >= new \DateTimeImmutable();
    }
}
