<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Order\Enum;

enum OrderCachePool: string
{
    case ORDERS_USER = 'orders_for_user_cache';
}
