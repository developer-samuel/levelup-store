<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Wishlist\Handler\Command;

use App\Core\Domain\Segment\Wishlist\Payload\WishlistPayload;

interface DestroyWishlistHandlerContract
{
    public function handle(WishlistPayload $payload): bool;
}
