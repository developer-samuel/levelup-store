<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Query;

use App\Core\Domain\{
    Segment\Password\Entity\PasswordResetToken,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Auth\Service\Query\ResetPasswordQueryContract,
    Segment\Password\PasswordResetTokenRepositoryContract
};

final readonly class ResetPasswordQueryService implements ResetPasswordQueryContract
{
    public function __construct(
        private PasswordResetTokenRepositoryContract $tokenRepository,
    ) {}

    public function getValidUserWithToken(?string $token): User
    {
        $tokenEntity = $this->getValidToken($token);
        if ($tokenEntity === null) {
            throw new \InvalidArgumentException('Token is invalid or expired.');
        }

        return $tokenEntity->getUser();
    }

    public function getValidToken(?string $token): ?PasswordResetToken
    {
        if ($token === null) {
            return null;
        }

        $entity = $this->tokenRepository->findByToken($token);

        if ($entity === null || !$this->isValidToken($entity)) {
            return null;
        }

        return $entity;
    }

    private function isValidToken(PasswordResetToken $entity): bool
    {
        return new \DateTimeImmutable('now') <= $entity->getExpiresAt();
    }
}
