<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Service\Query;

/**
 * @phpstan-type ProductRecommendedShape array{
 *     variant_id: int|string,
 *     name: string,
 *     imagePath: string,
 *     url: string,
 *     price: float,
 *     discountPrice: float|null,
 *     discount: bool,
 *     averageRating: float
 * }
*/
interface ProductRecommendedQueryContract
{
    /**
     * @return array<int, ProductRecommendedShape>
    */
    public function findAll(): array;
}
