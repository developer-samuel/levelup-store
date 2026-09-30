<?php

declare(strict_types=1);

namespace App\Adapters\External\MessageBroker;

use App\Core\Ports\Gateways\External\MessageBroker\RabbitMQGatewayContract;

final readonly class RabbitMQAdapter implements RabbitMQGatewayContract
{
    private const FALLBACK_DSN = 'doctrine://default?auto_setup=0';

    private string $dsn;

    /**
     * @param bool   $enabled
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $pass
     * @param string $vhost
    */
    public function __construct(
        private bool $enabled,
        private string $host,
        private int $port,
        private string $user,
        private string $pass,
        private string $vhost,
    ) {
        $this->dsn = sprintf(
            'amqp://%s:%s@%s:%d/%s',
            urlencode($this->user),
            urlencode($this->pass),
            $this->host,
            $this->port,
            ltrim($this->vhost, '/'),
        );
    }

    /**
     * @return bool
    */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string
    */
    public function getMessengerDsn(): string
    {
        return $this->enabled ? $this->dsn : self::FALLBACK_DSN;
    }

    /**
     * @return bool
    */
    public function isConnected(): bool
    {
        set_error_handler(static fn() => true);
        $connection = fsockopen($this->host, $this->port, timeout: 3);
        restore_error_handler();

        if ($connection === false) {
            return false;
        }

        fclose($connection);

        return true;
    }

    /**
     * @return string
    */
    public function getConnectionDsn(): string
    {
        return $this->dsn;
    }
}
