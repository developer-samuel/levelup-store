<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Enum\Variant;

use App\Shared\Traits\Enum\HasEnumLabel;

enum ProductVariantEanStatus: string
{
    use HasEnumLabel;

    case ACTIVE = 'active';
    case RESERVED = 'reserved';
    case SOLD = 'sold';
    case REFUNDED = 'refunded';
}
