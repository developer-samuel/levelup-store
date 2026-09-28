<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Event;

use App\Core\Domain\Segment\User\Entity\User;

final readonly class ResetPasswordCompletedEvent
{
    /**
     * @param User $user
    */
    public function __construct(
        public User $user,
    ) {}
}
