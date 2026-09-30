<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Email;

final readonly class OrderItemEmailObject
{
    /**
     * @param OrderVariantEmailObject $variant
     * @param float $price
     * @param string|null $imagePath
    */
    public function __construct(
        public OrderVariantEmailObject $variant,
        public float $price,
        public ?string $imagePath = null,
    ) {}
}
