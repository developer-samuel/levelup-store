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
    public function __construct(
        private PasswordHasherProviderContract $passwordHasherProxy,
    ) {}

    public function requireOldPassword(ChangePasswordPayload $payload, User $user): void
    {
        $this->checkPassword(
            $user,
            $payload->oldPassword,
            false,
            'Old password is incorrect.',
        );
    }

    public function requireNewPassword(ChangePasswordPayload $payload, User $user): void
    {
        $this->checkPassword(
            $user,
            $payload->newPassword,
            true,
            'The new password must be different from your current password.',
        );
    }

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
