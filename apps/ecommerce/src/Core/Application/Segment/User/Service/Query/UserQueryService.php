<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Query;

use Kit\Assertion\Domain\User\UserAssertion;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Ports\{
    Segment\User\Repository\UserRepositoryContract,
    Segment\User\Service\Query\UserQueryContract
};

final readonly class UserQueryService implements UserQueryContract
{
    /**
     * @param UserRepositoryContract $userRepository
    */
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    /**
     * @param string $email
     *
     * @return User
     *
     * @throws \InvalidArgumentException
    */
    public function findUserByEmailOrFail(string $email): User
    {
        $user = $this->userRepository->findByEmail($email);
        UserAssertion::assertExists($user);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return bool
    */
    public function isAdmin(User $user): bool
    {
        return $user->getRole() === UserRole::ADMIN;
    }
}
