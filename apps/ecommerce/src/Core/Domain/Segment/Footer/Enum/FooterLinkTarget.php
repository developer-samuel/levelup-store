<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Footer\Enum;

use App\Shared\{
    Traits\Enum\HasEnumLabel,
    Traits\Enum\HasEnumValue
};

enum FooterLinkTarget: string
{
    use HasEnumLabel;
    use HasEnumValue;

    case BLANK = '_blank';
    case SELF = '_self';
}
