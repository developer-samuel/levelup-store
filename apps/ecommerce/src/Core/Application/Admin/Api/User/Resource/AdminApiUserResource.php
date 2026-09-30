<?php

declare(strict_types=1);

namespace App\Core\Application\Admin\Api\User\Resource;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Shared\Utils\Formatter\DateTimeFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     id: int,
 *     name: string,
 *     email: string,
 *     role: UserRole,
 *     emailVerifiedAt: \DateTimeImmutable|null,
 *     createdAt: string
 * }
*/
final class AdminApiUserResource
{
    /**
     * @param User $user
     *
     * @return ResourceArray
    */
    public static function toArray(User $user): array
    {
        return [
            'id'              => $user->getId(),
            'name'            => $user->getFullName(),
            'email'           => $user->getEmail(),
            'role'            => $user->getRole(),
            'emailVerifiedAt' => $user->getEmailVerifiedAt(),
            'createdAt'       => DateTimeFormatter::formatDMY($user->getCreatedAt()),
        ];
    }
}
