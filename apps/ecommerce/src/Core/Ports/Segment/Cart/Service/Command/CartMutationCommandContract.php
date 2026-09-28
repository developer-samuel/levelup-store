<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Command;

interface CartMutationCommandContract
{
    /**
     * @param int $variantId
     *
     * @return array<string, mixed>
    */
    public function addToCart(int $variantId): array;

    /**
     * @param int $itemId
     *
     * @return array<string, mixed>
    */
    public function removeFromCart(int $itemId): array;
}
