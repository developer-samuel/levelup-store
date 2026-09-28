<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Service\Query;

use App\Core\Domain\Segment\Cart\ValueObject\CartItemObject;

use App\Core\Ports\{
    Segment\Cart\Service\Query\CartPriceQueryContract,
    Segment\Product\Service\Query\ProductPriceQueryContract
};

final class CartPriceQueryService implements CartPriceQueryContract
{
    /**
     * @param ProductPriceQueryContract $productPriceQuery
    */
    public function __construct(
        private ProductPriceQueryContract $productPriceQuery,
    ) {}

    /**
     * @param CartItemObject[] $items
     *
     * @return float
    */
    public function calculateTotalPrice(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $price = $this->productPriceQuery->getPrice($item->variant);
            $total += $price->discountedPrice;
        }

        return $total;
    }
}
