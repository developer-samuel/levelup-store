<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Utils;

use App\Core\Domain\Segment\Order\Enum\OrderStatus;

final class OrderStatusResolver
{
    /**
     * @param OrderStatus $status
     *
     * @return OrderStatus[]
    */
    public static function resolveAvailableStatuses(OrderStatus $status): array
    {
        $activeStatuses = self::getActiveStatuses();

        return match ($status) {
            OrderStatus::COMPLETED => [OrderStatus::COMPLETED, OrderStatus::REFUNDED],
            OrderStatus::REFUNDED  => [OrderStatus::REFUNDED],
            default                => in_array($status, $activeStatuses, true)
                ? array_merge($activeStatuses, [OrderStatus::COMPLETED, OrderStatus::REFUNDED])
                : [],
        };
    }

    /**
     * @return OrderStatus[]
    */
    private static function getActiveStatuses(): array
    {
        return array_map(
            static fn(string $s): OrderStatus => OrderStatus::from($s),
            OrderStatus::activeStatuses(),
        );
    }
}
