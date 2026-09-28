<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Cart\Service\Query;

use App\Core\Domain\Segment\Cart\ValueObject\CartItemObject;

/**
 * @phpstan-type CartSummary array{
 *     items: CartItemObject[],
 *     totalPrice: float,
 *     totalItems: int
 * }
 * @phpstan-type CartTotals array{
 *     totalItems: int,
 *     totalPrice: string
 * }
 * @phpstan-type CartResponse array{
 *     html: string,
 *     totalItems: int,
 *     totalPrice: string,
 *     message: string,
 *     success: bool,
 *     status: int|null
 * }
*/
interface CartSummaryQueryContract
{
    /**
     * @param int $userId
     *
     * @return CartSummary
    */
    public function getCartSummary(int $userId): array;

    /**
     * @param int $userId
     *
     * @return CartItemObject[] $items
    */
    public function findCartItemsForUser(int $userId): array;

    /**
     * @param string $message
     * @param string $html
     * @param CartTotals $summary
     *
     * @return CartResponse
    */
    public function buildSuccessResponse(string $message, string $html, array $summary): array;

    /**
     * @param string $message
     * @param string $html
     * @param CartTotals $summary
     * @param int $status
     *
     * @return CartResponse
    */
    public function buildErrorResponse(string $message, string $html, array $summary, int $status): array;
}
