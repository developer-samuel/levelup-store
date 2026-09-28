<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Security\Provider\PasswordHasherProviderContract,
    Segment\User\Service\Command\ChangePasswordCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class ChangePasswordCommandService implements ChangePasswordCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param PasswordHasherProviderContract $passwordHasherProxy
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private PasswordHasherProviderContract $passwordHasherProxy,
    ) {}

    /**
     * @param User $user
     * @param string $newPassword
     *
     * @return void
    */
    public function changeUserPassword(User $user, string $newPassword): void
    {
        $hashedPassword = $this->passwordHasherProxy->hash($user, $newPassword);
        $user->setPassword($hashedPassword);

        $this->entityPersistence->flush();
    }
}
