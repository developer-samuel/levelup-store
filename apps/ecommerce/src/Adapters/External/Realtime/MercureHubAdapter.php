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
    public function __construct(
        private HubInterface $hub,
        private bool $enabled,
        private string $hubUrl,
    ) {}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isConnected(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $parsed = parse_url($this->hubUrl);
        $host = $parsed['host'] ?? null;
        $port = $parsed['port'] ?? (($parsed['scheme'] ?? 'http') === 'https' ? 443 : 80);

        if ($host === null) {
            return false;
        }

        set_error_handler(static fn() => true);
        $connection = fsockopen($host, $port, timeout: 3);
        restore_error_handler();

        if ($connection === false) {
            return false;
        }

        fclose($connection);

        return true;
    }

    public function publish(string $topic, string $data): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            $this->hub->publish(new Update($topic, $data));
        } catch (\Throwable) {
            return;
        }
    }
}
