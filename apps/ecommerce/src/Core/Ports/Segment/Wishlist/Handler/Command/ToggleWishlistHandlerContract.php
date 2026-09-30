<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist\Handler\Command;

use App\Core\Domain\Segment\Wishlist\Payload\WishlistPayload;

interface ToggleWishlistHandlerContract
{
    /**
     * @param WishlistPayload $payload
     *
     * @return bool
    */
    public function handle(WishlistPayload $payload): bool;
}
