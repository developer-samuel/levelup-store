<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Event;

use App\Core\Domain\Segment\User\Entity\User;

final readonly class UserProfileUpdatedEvent
{
    public function __construct(
        public User $user,
    ) {}
}
