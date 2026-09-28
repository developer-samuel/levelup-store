<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Cart\ValueObject;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

/**
 * @phpstan-type ObjectArray array{
 *     id: int,
 *     cart_id: int,
 *     variant: ProductVariant,
 *     hasDiscount: bool,
 *     formattedPrice: string,
 *     formattedDiscountPrice: string|null,
 *     imagePath: string|null,
 *     averageRating: float
 * }
*/
final readonly class CartItemObject
{
    /**
     * @param int $id
     * @param int $cartId
     * @param ProductVariant $variant
     * @param string $formattedPrice
     * @param bool $hasDiscount
     * @param float $averageRating
     * @param float|null $discountPrice
     * @param string|null $formattedDiscountPrice
     * @param string|null $imagePath
    */
    public function __construct(
        public int $id,
        public int $cartId,
        public ProductVariant $variant,
        public string $formattedPrice,
        public bool $hasDiscount,
        public float $averageRating,
        public ?float $discountPrice = null,
        public ?string $formattedDiscountPrice = null,
        public ?string $imagePath = null,
    ) {}

    /**
     * @return ObjectArray
    */
    public function toArray(): array
    {
        return [
            'id'                     => $this->id,
            'cart_id'                => $this->cartId,
            'variant'                => $this->variant,
            'hasDiscount'            => $this->hasDiscount,
            'formattedPrice'         => $this->formattedPrice,
            'formattedDiscountPrice' => $this->formattedDiscountPrice,
            'imagePath'              => $this->imagePath,
            'averageRating'          => $this->averageRating,
        ];
    }
}
