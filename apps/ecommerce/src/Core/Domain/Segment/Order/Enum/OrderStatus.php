<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum OrderStatus: string
{
    use HasEnumLabel;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSED = 'processed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';

    private const ACTIVE_STATUSES = [
        self::PENDING,
        self::CONFIRMED,
        self::PROCESSED,
        self::SHIPPED,
        self::DELIVERED,
    ];
    private const COMPLETED_STATUSES = [
        self::COMPLETED,
        self::REFUNDED,
    ];
    private const FINAL_STATUSES = [
        self::DELIVERED,
        self::COMPLETED,
        self::REFUNDED,
    ];

    /**
     * @return string[]
    */
    public static function getAvailableStatuses(): array
    {
        return array_map(
            fn(self $status): string => $status->value,
            self::cases(),
        );
    }

    /**
     * @return string[]
    */
    public static function activeStatuses(): array
    {
        return array_map(
            fn(self $status): string => $status->value,
            self::ACTIVE_STATUSES,
        );
    }

    /**
     * @return string[]
    */
    public static function completedStatuses(): array
    {
        return array_map(
            fn(self $status): string => $status->value,
            self::COMPLETED_STATUSES,
        );
    }

    /**
     * @return bool
    */
    public function isFinalStatus(): bool
    {
        return in_array($this, self::FINAL_STATUSES, true);
    }

    /**
     * @param string $status
     *
     * @return bool
    */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::getAvailableStatuses(), true);
    }
}
