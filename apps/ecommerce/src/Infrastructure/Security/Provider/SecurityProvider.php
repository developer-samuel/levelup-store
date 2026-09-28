<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Provider;

use Symfony\{
    Bundle\SecurityBundle\Security,
    Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Security\Provider\SecurityProviderContract;

final readonly class SecurityProvider implements SecurityProviderContract
{
    /**
     * @param Security $security
     * @param TokenStorageInterface $tokenStorage
    */
    public function __construct(
        private Security $security,
        private TokenStorageInterface $tokenStorage,
    ) {}

    /**
     * @return User|null
     */
    public function getCurrentUser(): ?User
    {
        return $this->getUserFromSecurity() ?? $this->getTokenStorage();
    }

    /**
     * @return User|null
    */
    private function getUserFromSecurity(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }

    /**
     * @return User|null
    */
    private function getTokenStorage(): ?User
    {
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        return $user instanceof User ? $user : null;
    }
}
