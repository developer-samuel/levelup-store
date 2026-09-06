<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Realtime;

interface MercureHubGatewayContract
{
    /**
     * @return bool
    */
    public function isEnabled(): bool;

    /**
     * @return bool
    */
    public function isConnected(): bool;

    /**
     * @param string $topic
     * @param string $data
     *
     * @return void
    */
    public function publish(string $topic, string $data): void;
}
