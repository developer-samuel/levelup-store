<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Product\Enum;

enum ProductCacheKeyPrefix: string
{
    case TITLE = 'product_title_prefix_';
    case ROUTE = 'product_route_prefix_';
}
