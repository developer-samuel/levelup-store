<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constants;

final class SecurityConstants
{
    public const FIREWALL_NAME = 'main';
    public const SESSION_TOKEN_KEY = '_security_' . self::FIREWALL_NAME;
}
