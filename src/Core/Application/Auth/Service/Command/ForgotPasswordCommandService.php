<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Domain\{
    Segment\Password\Entity\PasswordResetToken,
    Segment\User\Entity\User
};

use App\Core\Application\Shared\Utils\CodeGenerator;

use App\Core\Ports\{
    Auth\Notifier\ForgotPasswordNotifierContract,
    Auth\Service\Command\ForgotPasswordCommandContract,
    Segment\Password\PasswordResetTokenRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class ForgotPasswordCommandService implements ForgotPasswordCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param PasswordResetTokenRepositoryContract $tokenRepository
     * @param ForgotPasswordNotifierContract $notifier
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private PasswordResetTokenRepositoryContract $tokenRepository,
        private ForgotPasswordNotifierContract $notifier,
    ) {}

    /**
     * @param User $user
     *
     * @return void
    */
    public function createAndSaveTokenForUser(User $user): void
    {
        $this->removeExistingTokens($user);

        $token = CodeGenerator::generateUnique(128);
        $expiresAt = new \DateTimeImmutable('+15 minutes');

        $passwordResetToken = $this->createTokenEntity($user, $token, $expiresAt);

        $this->entityPersistence->persist($passwordResetToken, true);

        $this->notifier->send($user, $token);
    }

    /**
     * @param User $user
     *
     * @return void
    */
    private function removeExistingTokens(User $user): void
    {
        $this->tokenRepository->removeTokensByUser($user);
    }

    /**
     * @param User $user
     * @param string $token
     * @param \DateTimeImmutable $expiresAt
     *
     * @return PasswordResetToken
    */
    private function createTokenEntity(
        User $user,
        string $token,
        \DateTimeImmutable $expiresAt,
    ): PasswordResetToken {
        return (new PasswordResetToken())
            ->setUser($user)
            ->setToken($token)
            ->setExpiresAt($expiresAt);
    }
}
