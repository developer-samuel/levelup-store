<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constants;

final class CookieConstants
{
    public const NAME = 'cookie_consent';
    public const VALUE = 'true';
    public const DURATION = '+1 year';
    public const PATH = '/';
    public const SECURE = true;
    public const HTTP_ONLY = true;
}
