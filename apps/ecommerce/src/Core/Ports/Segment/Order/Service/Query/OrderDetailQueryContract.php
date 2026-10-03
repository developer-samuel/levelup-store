<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Order\Service\Query;

use App\Core\Domain\{
    Segment\Order\Entity\Order,
    Segment\Order\ValueObject\OrderItemObject
};

/**
 * @phpstan-type ItemsWithTotal array{
 *     items: OrderItemObject[],
 *     total: float
 * }
*/
interface OrderDetailQueryContract
{
    public function fetchOrder(string $code): ?Order;

    /** @return ItemsWithTotal */
    public function buildItemsWithTotal(Order $order): array;
}
