<?php

declare(strict_types=1);

namespace App\Adapters\External\Realtime;

use Symfony\Component\Mercure\{
    HubInterface,
    Update
};

use App\Core\Ports\Gateways\External\Realtime\MercureHubGatewayContract;

final readonly class MercureHubAdapter implements MercureHubGatewayContract
{
    /**
     * @param HubInterface $hub
     * @param bool $enabled
    */
    public function __construct(
        private HubInterface $hub,
        private bool $enabled,
    ) {}

    /**
     * @param string $topic
     * @param string $data
     *
     * @return void
    */
    public function publish(string $topic, string $data): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->hub->publish(new Update($topic, $data));
    }
}
