<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Enum;

enum OrderCacheKeyPrefix: string
{
    case ORDERS_USER = 'orders_for_user_prefix_';
}
