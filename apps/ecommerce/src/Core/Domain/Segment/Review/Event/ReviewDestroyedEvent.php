<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Event;

use App\Core\Domain\Segment\User\Entity\User;

final readonly class ReviewDestroyedEvent
{
    public function __construct(
        public int $reviewId,
        public User $user,
    ) {}
}
