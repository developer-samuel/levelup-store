<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Notifier;

use App\Core\Domain\Segment\User\Entity\User;

interface UserNotifierContract
{
    public function notifyLoggedIn(User $user): void;
    public function notifyRegistered(User $user): void;
    public function notifyEmailVerified(User $user): void;
}
