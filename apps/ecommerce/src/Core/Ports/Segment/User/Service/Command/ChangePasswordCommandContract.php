<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface ChangePasswordCommandContract
{
    public function changeUserPassword(User $user, string $newPassword): void;
}
