<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Enum;

use App\Shared\{
    Traits\Enum\HasEnumLabel,
    Traits\Enum\HasEnumValue
};

enum UserRole: string
{
    use HasEnumLabel;
    use HasEnumValue;

    case USER = 'user';
    case ADMIN = 'admin';
}
