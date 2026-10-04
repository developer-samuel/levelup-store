<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Auth;

interface TokenBlacklistContract
{
    public function blacklist(string $token, \DateTimeImmutable $expiresAt): void;
    public function isBlacklisted(string $token): bool;
}
