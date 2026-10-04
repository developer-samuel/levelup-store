<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Repository;

use App\Core\Domain\{
    Segment\Cart\Entity\Cart,
    Segment\Cart\Entity\CartItem
};

interface CartItemRepositoryContract
{
    public function getItem(int $itemId): ?CartItem;

    /** @return CartItem[] */
    public function findByCart(Cart $cart): array;

    /** @return CartItem[] */
    public function findAllWithVariant(): array;
}
