<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Audit\Enum;

enum AuditAction: string
{
    // Auth
    case LOGIN = 'LOGIN';
    case SIGNUP = 'SIGNUP';
    case EMAIL_VERIFIED = 'EMAIL_VERIFIED';
    case PASSWORD_RESET = 'PASSWORD_RESET';

    // User
    case PROFILE_UPDATE = 'PROFILE_UPDATE';
    case PROFILE_DESTROY = 'PROFILE_DESTROY';
    case PASSWORD_CHANGE = 'PASSWORD_CHANGE';

    // Orders
    case ORDER_CREATED = 'ORDER_CREATED';
    case ORDER_STATUS_CHANGE = 'ORDER_STATUS_CHANGE';
}
