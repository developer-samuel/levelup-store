<?php

declare(strict_types=1);

namespace App\Shared\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum AddressFields: string
{
    use HasEnumLabel;

    case COUNTRY = 'country';
    case STREET = 'street';
    case POSTAL_CODE = 'postal_code';
    case CITY = 'city';
}
