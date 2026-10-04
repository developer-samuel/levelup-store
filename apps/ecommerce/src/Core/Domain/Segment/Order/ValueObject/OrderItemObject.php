<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

final readonly class OrderItemObject
{
    public function __construct(
        public ProductVariant $variant,
        public string $imagePath,
        public string $url,
        public float $price,
    ) {}
}
