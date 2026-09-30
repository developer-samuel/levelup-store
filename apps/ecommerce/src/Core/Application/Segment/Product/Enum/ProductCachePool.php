<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Enum;

enum ProductCachePool: string
{
    case TITLE = 'product_title_cache';
    case ROUTE = 'product_route_cache';
}
