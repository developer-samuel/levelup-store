<?php

declare(strict_types=1);

namespace Presets\Kit\Assertion\Domain\User;

use Presets\Kit\Assertion\Shared\ExistenceAssertion;

use App\Core\Domain\Segment\User\Entity\User;

final class UserAssertion
{
    /** @phpstan-assert User $user */
    public static function assertExists(?User $user): void
    {
        ExistenceAssertion::assertExists($user, 'User');
    }

    /** @phpstan-assert User $user */
    public static function assertInstance(mixed $user): User
    {
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found.');
        }

        return $user;
    }
}
