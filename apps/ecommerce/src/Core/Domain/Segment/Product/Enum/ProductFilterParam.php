<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Enum;

use App\Shared\Traits\Enum\HasEnumValue;

enum ProductFilterParam: string
{
    use HasEnumValue;

    case BRAND = 'brand';
    case MIN_PRICE = 'minPrice';
    case MAX_PRICE = 'maxPrice';
}
