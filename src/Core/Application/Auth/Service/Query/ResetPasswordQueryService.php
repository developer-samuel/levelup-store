<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Query;

use App\Core\Domain\{
    Shared\Exception\NotFoundException,
    Segment\Password\Entity\PasswordResetToken,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Auth\Service\Query\ResetPasswordQueryContract,
    Segment\Password\PasswordResetTokenRepositoryContract
};

final readonly class ResetPasswordQueryService implements ResetPasswordQueryContract
{
    /**
     * @param PasswordResetTokenRepositoryContract $tokenRepository
    */
    public function __construct(
        private PasswordResetTokenRepositoryContract $tokenRepository,
    ) {}

    /**
     * @param string|null $token
     *
     * @return User
     *
     * @throws \InvalidArgumentException
     * @throws NotFoundException
    */
    public function getValidUserWithToken(?string $token): User
    {
        $tokenEntity = $this->getValidToken($token);
        if ($tokenEntity === null) {
            throw new \InvalidArgumentException('Token is invalid or expired.');
        }

        return $tokenEntity->getUser();
    }

    /**
     * @param string|null $token
     *
     * @return PasswordResetToken|null
    */
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

    /**
     * @param PasswordResetToken $entity
     *
     * @return bool
    */
    private function isValidToken(PasswordResetToken $entity): bool
    {
        return new \DateTimeImmutable('now') <= $entity->getExpiresAt();
    }
}
