<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Service\Command;

use App\Core\Domain\Segment\User\Entity\User;

interface ChangePasswordCommandContract
{
    /**
     * @param User $user
     * @param string $newPassword
     *
     * @return void
    */
    public function changeUserPassword(User $user, string $newPassword): void;
}
