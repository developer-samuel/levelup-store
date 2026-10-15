<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Wishlist\Event;

use App\Core\Domain\Segment\User\Entity\User;

final readonly class WishlistItemAddedEvent
{
    public function __construct(
        public User $user,
        public int $variantId,
    ) {}
}
