<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Wishlist\Payload;

final readonly class WishlistPayload
{
    public function __construct(
        public int $variantId,
    ) {}
}
