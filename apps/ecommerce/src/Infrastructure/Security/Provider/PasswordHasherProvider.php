<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Provider;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Security\Provider\PasswordHasherProviderContract;

final readonly class PasswordHasherProvider implements PasswordHasherProviderContract
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
    */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    /**
     * @param User $user
     * @param string $password
    */
    public function hash(User $user, string $password): string
    {
        return $this->passwordHasher->hashPassword(
            $user,
            $password,
        );
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return bool
    */
    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $password);
    }
}
