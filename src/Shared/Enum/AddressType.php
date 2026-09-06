<?php

declare(strict_types=1);

namespace App\Shared\Enum;

use App\Shared\Traits\Enum\HasEnumLabel;

enum AddressType: string
{
    use HasEnumLabel;

    case BILLING = 'billing';
    case SHIPPING = 'shipping';
}
