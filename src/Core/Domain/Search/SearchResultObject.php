<?php

declare(strict_types=1);

namespace App\Core\Domain\Search;

final readonly class SearchResultObject
{
    /**
     * @param string $name
     * @param float $price
     * @param string $url
     * @param string $image
     * @param float|null $discountPrice
     * @param bool $hasDiscount
     * @param float $averageRating
    */
    public function __construct(
        public string $name,
        public float $price,
        public string $url,
        public string $image,
        public ?float $discountPrice,
        public bool $hasDiscount,
        public float $averageRating,
    ) {}
}
