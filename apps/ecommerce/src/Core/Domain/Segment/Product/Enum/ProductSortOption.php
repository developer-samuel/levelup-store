<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum ProductSortOption: string
{
    use HasEnumLabel;

    case TOP_RATED = 'top_rated';
    case LATEST = 'latest';
    case CHEAPEST = 'cheapest';
    case MOST_EXPENSIVE = 'most_expensive';
}
