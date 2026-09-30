<?php

declare(strict_types=1);

namespace Tests\Support\Mocks;

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Ports\Gateways\External\Turnstile\TurnstileGatewayContract;

trait TurnstileMock
{
    private bool $turnstileVerified = true;

    private function createTurnstileMock(): TurnstileGatewayContract&MockObject
    {
        $turnstile = $this->createMock(TurnstileGatewayContract::class);
        $turnstile->method('isEnabled')->willReturn(true);
        $turnstile->method('verify')->willReturnCallback(fn (): bool => $this->turnstileVerified);

        return $turnstile;
    }
}
