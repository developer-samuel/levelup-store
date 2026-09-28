<?php

declare(strict_types=1);

namespace App\Shared\Constants;

final class PathConstants
{
    public const GUEST_PATHS = [
        '/login',
        '/signup',
        '/forgot-password',
        '/reset-password',
    ];
    public const SECURITY_PATHS = [
        '/must-verify',
        '/orders',
        '/profile',
        '/change-password',
    ];
    public const VERIFY_BASE_PATH = '/must-verify';
    public const PRODUCTS_BASE_PATH = '/products';
    public const ADMIN_BASE_PATH = '/admin';
}
