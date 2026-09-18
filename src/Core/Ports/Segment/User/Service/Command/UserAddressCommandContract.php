<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Command;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping
};

interface UserAddressCommandContract
{
    /**
     * @param User $user
     * @param UserBilling|UserShipping|null $entity
     * @param array<string, int|string|null> $data
     * @param class-string<UserBilling|UserShipping> $entityClass
     *
     * @return void
    */
    public function processAddressEntity(
        User $user,
        UserBilling|UserShipping|null $entity,
        array $data,
        string $entityClass,
    ): void;
}
