<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Query;

use App\Core\Domain\{
    Segment\User\Entity\User,
    Segment\User\Payload\ChangePasswordPayload
};

interface ChangePasswordQueryContract
{
    public function requireOldPassword(ChangePasswordPayload $payload, User $user): void;
    public function requireNewPassword(ChangePasswordPayload $payload, User $user): void;
}
