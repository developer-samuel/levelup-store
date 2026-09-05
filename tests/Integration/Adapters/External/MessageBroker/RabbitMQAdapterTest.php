<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\MessageBroker;

use PHPUnit\Framework\TestCase;

use App\Core\Ports\Gateways\External\MessageBroker\RabbitMQGatewayContract;

use App\Adapters\External\MessageBroker\RabbitMQAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\MessageBroker\RabbitMQAdapter
*/
class RabbitMQAdapterTest extends TestCase
{
    private string $host;
    private int $port;
    private string $user;
    private string $pass;
    private string $vhost;
    private RabbitMQAdapter $adapter;

    protected function setUp(): void
    {
        $host  = $_ENV['RABBITMQ_HOST']  ?? '127.0.0.1';
        $port  = $_ENV['RABBITMQ_PORT']  ?? 5672;
        $user  = $_ENV['RABBITMQ_USER']  ?? 'guest';
        $pass  = $_ENV['RABBITMQ_PASS']  ?? 'guest';
        $vhost = $_ENV['RABBITMQ_VHOST'] ?? '/';

        $this->host  = is_string($host)  ? $host  : '127.0.0.1';
        $this->port  = is_numeric($port) ? (int) $port : 5672;
        $this->user  = is_string($user)  ? $user  : 'guest';
        $this->pass  = is_string($pass)  ? $pass  : 'guest';
        $this->vhost = is_string($vhost) ? $vhost : '/';

        $this->adapter = new RabbitMQAdapter(true, $this->host, $this->port, $this->user, $this->pass, $this->vhost);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(RabbitMQGatewayContract::class, $this->adapter);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $this->assertTrue($this->adapter->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $adapter = new RabbitMQAdapter(false, $this->host, $this->port, $this->user, $this->pass, $this->vhost);

        $this->assertFalse($adapter->isEnabled());
    }

    public function testIsConnectedReturnsTrueWhenRunning(): void
    {
        $this->skipIfNotConnected();
        $this->assertTrue($this->adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenUnreachable(): void
    {
        $adapter = new RabbitMQAdapter(true, '127.0.0.1', 19999, $this->user, $this->pass, $this->vhost);

        $this->assertFalse($adapter->isConnected());
    }

    public function testGetMessengerDsnReturnsAmqpDsnWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        $this->assertStringStartsWith('amqp://', $this->adapter->getMessengerDsn());
    }

    public function testGetMessengerDsnReturnsFallbackDsnWhenDisabled(): void
    {
        $adapter = new RabbitMQAdapter(false, $this->host, $this->port, $this->user, $this->pass, $this->vhost);

        $this->assertStringStartsWith('doctrine://', $adapter->getMessengerDsn());
    }

    public function testGetConnectionDsnReturnsAmqpDsn(): void
    {
        $this->skipIfNotConnected();
        $this->assertStringStartsWith('amqp://', $this->adapter->getConnectionDsn());
    }

    public function testGetConnectionDsnContainsHost(): void
    {
        $this->skipIfNotConnected();
        $this->assertStringContainsString($this->host, $this->adapter->getConnectionDsn());
    }

    public function testGetConnectionDsnContainsPort(): void
    {
        $this->skipIfNotConnected();
        $this->assertStringContainsString((string) $this->port, $this->adapter->getConnectionDsn());
    }

    public function testGetConnectionDsnContainsCredentials(): void
    {
        $this->skipIfNotConnected();
        $this->assertStringContainsString($this->user, $this->adapter->getConnectionDsn());
    }

    public function testGetMessengerDsnAndConnectionDsnAreConsistentWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        $this->assertSame($this->adapter->getConnectionDsn(), $this->adapter->getMessengerDsn());
    }

    private function skipIfNotConnected(): void
    {
        if (!$this->adapter->isConnected()) {
            $this->markTestSkipped('RabbitMQ is not available.');
        }
    }
}
