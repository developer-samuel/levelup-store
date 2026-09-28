<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Enum\Variant;

use App\Shared\Traits\Enum\HasEnumLabel;

enum ProductVariantStatus: string
{
    use HasEnumLabel;

    case AVAILABLE = 'available';
    case HIDDEN = 'hidden';
}
