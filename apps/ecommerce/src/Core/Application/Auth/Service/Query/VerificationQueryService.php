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
    public function __construct(
        private UserVerificationTokenRepositoryContract $tokenRepository,
    ) {}

    public function getValidToken(string $token): ?UserVerificationToken
    {
        $tokenEntity = $this->tokenRepository->findByToken($token);

        if ($tokenEntity === null || !$this->isTokenValid($tokenEntity)) {
            return null;
        }

        return $tokenEntity;
    }

    public function isUserVerifiable(?User $user): bool
    {
        return $user !== null && $user->getEmailVerifiedAt() === null;
    }

    private function isTokenValid(UserVerificationToken $token): bool
    {
        return $token->getExpiresAt() >= new \DateTimeImmutable();
    }
}
