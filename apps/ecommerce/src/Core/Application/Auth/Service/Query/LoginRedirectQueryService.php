<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Enum\UserRole
};

use App\Core\Ports\Auth\Service\Query\LoginRedirectQueryContract;

final class LoginRedirectQueryService implements LoginRedirectQueryContract
{
    /**
     * @param User $user
     *
     * @return string
    */
    public function getRedirectRoute(User $user): string
    {
        return $user->getRole()->value === UserRole::ADMIN->value ? '/admin' : '/';
    }
}
