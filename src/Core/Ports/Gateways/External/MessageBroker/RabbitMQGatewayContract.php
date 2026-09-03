<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\MessageBroker;

interface RabbitMQGatewayContract
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
     * @return string
    */
    public function getConnectionDsn(): string;

    /**
     * @return string
    */
    public function getMessengerDsn(): string;
}
