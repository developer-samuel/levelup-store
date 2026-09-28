<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum ProductTitleType: string
{
    use HasEnumLabel;

    case EMPTY = 'empty';
    case CATEGORY_ONLY = 'category_only';
    case BOTH = 'both';
}
