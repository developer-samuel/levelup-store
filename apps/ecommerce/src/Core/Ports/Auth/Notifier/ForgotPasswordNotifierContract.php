<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Notifier;

use App\Core\Domain\Segment\User\Entity\User;

interface ForgotPasswordNotifierContract
{
    /**
     * @param User $user
     * @param string $token
     *
     * @return void
    */
    public function send(User $user, string $token): void;
}
