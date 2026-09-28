<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Cart\Resource;

use App\Core\Domain\Segment\Cart\ValueObject\CartItemObject;

use App\Shared\Utils\Formatter\PriceFormatter;

/**
 * @phpstan-type ResourceArray array{
 *     totalItems: int,
 *     totalPrice: string
 * }
*/
final class CartSummaryResource
{
    /**
     * @param CartItemObject[] $items
     * @param float $totalPrice
     *
     * @return ResourceArray
    */
    public static function toArray(array $items, float $totalPrice): array
    {
        return [
            'totalItems' => count($items),
            'totalPrice' => PriceFormatter::format($totalPrice),
        ];
    }
}
