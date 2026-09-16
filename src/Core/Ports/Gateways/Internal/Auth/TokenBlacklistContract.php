<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Auth;

interface TokenBlacklistContract
{
    /**
     * @param string $token
     * @param \DateTimeImmutable $expiresAt
     *
     * @return void
    */
    public function blacklist(string $token, \DateTimeImmutable $expiresAt): void;

    /**
     * @param string $token
     *
     * @return bool
    */
    public function isBlacklisted(string $token): bool;
}
