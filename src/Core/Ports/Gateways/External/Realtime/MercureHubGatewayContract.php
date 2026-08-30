<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Realtime;

interface MercureHubGatewayContract
{
    /**
     * @param string $topic
     * @param string $data
     *
     * @return void
    */
    public function publish(string $topic, string $data): void;
}
