<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Turnstile;

interface TurnstileGatewayContract
{
    public function isEnabled(): bool;
    public function verify(string $token, string $ip): bool;
}
