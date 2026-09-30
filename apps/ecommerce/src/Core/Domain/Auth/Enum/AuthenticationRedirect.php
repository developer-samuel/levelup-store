<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Enum;

enum AuthenticationRedirect: string
{
    case HOME = 'home';
    case ADMIN_DASHBOARD = 'admin';
}
