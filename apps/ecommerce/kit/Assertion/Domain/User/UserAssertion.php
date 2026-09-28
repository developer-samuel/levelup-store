<?php

declare(strict_types=1);

namespace Kit\Assertion\Domain\User;

use Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\User\Entity\User;

final class UserAssertion
{
    /**
     * @param User|null $user
     *
     * @return void
     *
     * @phpstan-assert User $user
    */
    public static function assertExists(?User $user): void
    {
        ExistenceAssertion::assertExists($user, 'User');
    }

    /**
     * @param mixed $user
     *
     * @return User
     *
     * @throws \RuntimeException
     *
     * @phpstan-assert User $user
    */
    public static function assertInstance(mixed $user): User
    {
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found.');
        }

        return $user;
    }
}
