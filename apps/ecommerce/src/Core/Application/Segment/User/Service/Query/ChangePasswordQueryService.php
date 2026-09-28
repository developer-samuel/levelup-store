<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Payload\ChangePasswordPayload
};

use App\Core\Ports\{
    Security\Provider\PasswordHasherProviderContract,
    Segment\User\Service\Query\ChangePasswordQueryContract
};

final readonly class ChangePasswordQueryService implements ChangePasswordQueryContract
{
    /**
     * @param PasswordHasherProviderContract $passwordHasherProxy
    */
    public function __construct(
        private PasswordHasherProviderContract $passwordHasherProxy,
    ) {}

    /**
     * @param ChangePasswordPayload $payload
     * @param User $user
     *
     * @return void
     *
     * @throws \InvalidArgumentException
    */
    public function requireOldPassword(ChangePasswordPayload $payload, User $user): void
    {
        $this->checkPassword(
            $user,
            $payload->oldPassword,
            false,
            'Old password is incorrect.',
        );
    }

    /**
     * @param ChangePasswordPayload $payload
     * @param User $user
     *
     * @return void
    */
    public function requireNewPassword(ChangePasswordPayload $payload, User $user): void
    {
        $this->checkPassword(
            $user,
            $payload->newPassword,
            true,
            'The new password must be different from your current password.',
        );
    }

    /**
     * @param User $user
     * @param mixed $password
     * @param bool $shouldBeDifferent
     * @param string $errorMessage
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \DomainException
    */
    private function checkPassword(User $user, mixed $password, bool $shouldBeDifferent, string $errorMessage): void
    {
        if (!is_string($password) || $password === '') {
            throw new \InvalidArgumentException('Password must be a non-empty string.');
        }

        $isValid = $this->passwordHasherProxy->isPasswordValid($user, $password);

        if ($shouldBeDifferent === $isValid) {
            throw new \DomainException($errorMessage);
        }
    }
}
