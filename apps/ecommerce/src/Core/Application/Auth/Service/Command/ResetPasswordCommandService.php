<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Auth\Notifier\ResetPasswordNotifierContract,
    Auth\Service\Command\ResetPasswordCommandContract,
    Security\Provider\PasswordHasherProviderContract,
    Segment\Password\PasswordResetTokenRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class ResetPasswordCommandService implements ResetPasswordCommandContract
{
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private PasswordHasherProviderContract $passwordHasherProxy,
        private PasswordResetTokenRepositoryContract $tokenRepository,
        private ResetPasswordNotifierContract $notifier,
    ) {}

    public function resetPassword(User $user, string $password): void
    {
        $this->changePassword($user, $password);
        $this->tokenRepository->removeTokensByUser($user);

        $this->notifier->send($user);
    }

    private function changePassword(User $user, string $newPassword): void
    {
        $hashedPassword = $this->passwordHasherProxy->hash($user, $newPassword);

        $user->setPassword($hashedPassword);

        $this->entityPersistence->flush();
    }
}
