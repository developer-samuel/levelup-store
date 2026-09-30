<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Message;

final readonly class ProductVariantRemoveMessage
{
    /**
     * @param int $variantId
    */
    public function __construct(
        public int $variantId,
    ) {}
}
