<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\User\Notifier;

use App\Core\Domain\Segment\User\Entity\User;

interface UserNotifierContract
{
    public function notifyPasswordChanged(User $user): void;
    public function notifyProfileDestroyed(User $user): void;
    public function notifyProfileUpdated(User $user): void;
}
