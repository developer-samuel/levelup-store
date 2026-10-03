<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Command;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Auth\Repository\RefreshTokenRepositoryContract,
    Segment\User\Service\Command\DestroyProfileCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class DestroyProfileCommandService implements DestroyProfileCommandContract
{
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private TokenStorageInterface $tokenStorage,
        private RefreshTokenRepositoryContract $refreshTokenRepository,
    ) {}

    public function destroyProfile(User $user): void
    {
        $this->refreshTokenRepository->removeTokensByUser($user);

        $user->setDeletedAt();

        $this->entityPersistence->persist($user, true);
        $this->tokenStorage->setToken(null);
    }
}
