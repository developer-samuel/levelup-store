<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Realtime;

interface MercureHubGatewayContract
{
    public function isEnabled(): bool;
    public function isConnected(): bool;
    public function publish(string $topic, string $data): void;
}
