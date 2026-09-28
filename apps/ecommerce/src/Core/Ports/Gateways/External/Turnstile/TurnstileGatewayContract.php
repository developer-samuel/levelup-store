<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Turnstile;

interface TurnstileGatewayContract
{
    /**
     * @return bool
    */
    public function isEnabled(): bool;

    /**
     * @param string $token
     * @param string $ip
     *
     * @return bool
    */
    public function verify(string $token, string $ip): bool;
}
