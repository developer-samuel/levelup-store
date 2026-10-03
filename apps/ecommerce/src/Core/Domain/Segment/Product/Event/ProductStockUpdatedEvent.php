<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Event;

final readonly class ProductStockUpdatedEvent
{
    public function __construct(
        public int $variantId,
        public int $quantityAvailable,
        public bool $inStock,
    ) {}
}
