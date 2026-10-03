<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Provider;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Security\Provider\PasswordHasherProviderContract;

final readonly class PasswordHasherProvider implements PasswordHasherProviderContract
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function hash(User $user, string $password): string
    {
        return $this->passwordHasher->hashPassword(
            $user,
            $password,
        );
    }

    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $password);
    }
}
