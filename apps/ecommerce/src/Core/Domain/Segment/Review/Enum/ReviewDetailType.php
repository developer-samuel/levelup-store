<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Enum;

use App\Shared\{
    Traits\Enum\HasEnumLabel,
    Traits\Enum\HasEnumValue
};

enum ReviewDetailType: string
{
    use HasEnumLabel;
    use HasEnumValue;

    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';
}
