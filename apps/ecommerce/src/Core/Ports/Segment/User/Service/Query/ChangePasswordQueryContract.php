<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Payload\ChangePasswordPayload
};

interface ChangePasswordQueryContract
{
    /**
     * @param ChangePasswordPayload $payload
     * @param User $user
     *
     * @return void
    */
    public function requireOldPassword(ChangePasswordPayload $payload, User $user): void;

    /**
     * @param ChangePasswordPayload $payload
     * @param User $user
     *
     * @return void
    */
    public function requireNewPassword(ChangePasswordPayload $payload, User $user): void;
}
