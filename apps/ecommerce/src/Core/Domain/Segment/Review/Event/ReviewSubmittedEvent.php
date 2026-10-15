<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Event;

use App\Core\Domain\Segment\User\Entity\User;

final readonly class ReviewSubmittedEvent
{
    public function __construct(
        public int $variantId,
        public User $user,
    ) {}
}
