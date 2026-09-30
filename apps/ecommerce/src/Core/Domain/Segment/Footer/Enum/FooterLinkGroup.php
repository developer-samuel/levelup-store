<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Footer\Enum;

use App\Shared\{
    Traits\Enum\HasEnumLabel,
    Traits\Enum\HasEnumValue
};

enum FooterLinkGroup: string
{
    use HasEnumLabel;
    use HasEnumValue;

    case CONTACT = 'contact';
    case CONNECT = 'connect';
    case LEGAL = 'legal';
}
