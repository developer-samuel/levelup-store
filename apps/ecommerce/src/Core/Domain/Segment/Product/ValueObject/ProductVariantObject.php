<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\ValueObject;

final readonly class ProductVariantObject
{
    /**
     * @param int $variantId
     * @param float $price
     * @param float $discountPrice
     * @param string $imagePath
     * @param string $name
     * @param string $url
     * @param string $createdAt
     * @param float|null $discount
     * @param float|null $averageRating
    */
    public function __construct(
        public int $variantId,
        public float $price,
        public float $discountPrice,
        public string $imagePath,
        public string $name,
        public string $url,
        public string $createdAt,
        public ?float $discount = null,
        public ?float $averageRating = null,
    ) {}
}
