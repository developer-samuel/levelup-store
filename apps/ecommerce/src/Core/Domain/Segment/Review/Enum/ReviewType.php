<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Enum;

use App\Shared\{
    Traits\Enum\HasEnumLabel,
    Traits\Enum\HasEnumValue
};

enum ReviewType: string
{
    use HasEnumLabel;
    use HasEnumValue;

    case RATING = 'rating';
    case FEEDBACK = 'feedback';
}
