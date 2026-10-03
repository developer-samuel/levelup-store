<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Notifier;

use App\Core\Domain\Segment\User\Entity\User;

interface ResetPasswordNotifierContract
{
    public function send(User $user): void;
}
