<?php

declare(strict_types=1);

namespace App\Core\Application\Security;

use Packages\Kit\Utils\Shared\StringNormalizer;

use App\Core\Domain\{
    Shared\Exception\AccessDeniedException,
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Ports\{
    Security\SecurityPolicyContract,
    Security\Provider\SecurityProviderContract
};

final readonly class SecurityPolicy implements SecurityPolicyContract
{
    public function __construct(
        private SecurityProviderContract $securityProvider,
    ) {}

    public function checkAccess(): User
    {
        return $this->runChecks(['user']);
    }

    public function checkIfEmailVerified(): User
    {
        return $this->runChecks(['user', 'email']);
    }

    public function checkAdminAccess(): User
    {
        return $this->runChecks(['user', 'email', 'admin']);
    }

    /** @param array<int, string> $checks */
    private function runChecks(array $checks): User
    {
        $user = $this->securityProvider->getCurrentUser();
        if ($user === null) {
            throw new AccessDeniedException('No user found.');
        }

        foreach ($checks as $check) {
            match ($check) {
                'user'  => $user = $this->validateUser($user),
                'email' => $user = $this->validateEmailVerified($user),
                'admin' => $user = $this->validateAdminRole($user),
                default => null,
            };
        }

        return $user;
    }

    private function validateUser(User $user): User
    {
        if (!$this->validateRoles($user, array_values(UserRole::values()))) {
            throw new AccessDeniedException('Access denied.');
        }

        return $user;
    }

    private function validateEmailVerified(User $user): User
    {
        if ($user->getEmailVerifiedAt() === null) {
            throw new AccessDeniedException('Email not verified.');
        }

        return $user;
    }

    private function validateAdminRole(User $user): User
    {
        if (!$this->validateRoles($user, [UserRole::ADMIN->value])) {
            throw new AccessDeniedException('Admin role required.');
        }

        return $user;
    }

    /** @param array<int, string> $allowedRoles */
    private function validateRoles(User $user, array $allowedRoles): bool
    {
        $roles = $user->getRoles();

        foreach ($roles as $role) {
            $normalized = StringNormalizer::toLowerCase(str_replace('ROLE_', '', $role));
            if (in_array($normalized, $allowedRoles, true)) {
                return true;
            }
        }

        return false;
    }
}
