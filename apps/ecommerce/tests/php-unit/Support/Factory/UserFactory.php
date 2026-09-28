<?php

declare(strict_types=1);

namespace Tests\Support\Factory;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

trait UserFactory
{
    private function createAndPersistUser(string $email = 'user@test.com'): User
    {
        $uniqueEmail = str_replace('@', '-' . uniqid() . '@', $email);

        $user = (new User())
            ->setEmail($uniqueEmail)
            ->setFirstName('Test')
            ->setLastName('User')
            ->setPassword('hashed-password')
            ->setRole(UserRole::USER);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
