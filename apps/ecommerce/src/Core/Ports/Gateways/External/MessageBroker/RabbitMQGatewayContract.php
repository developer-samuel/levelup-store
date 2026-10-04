<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\MessageBroker;

interface RabbitMQGatewayContract
{
    public function isEnabled(): bool;
    public function isConnected(): bool;
    public function getConnectionDsn(): string;
    public function getMessengerDsn(): string;
}
