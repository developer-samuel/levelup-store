<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\MessageBroker;

use PHPUnit\Framework\TestCase;

use App\Adapters\External\MessageBroker\RabbitMQAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\MessageBroker\RabbitMQAdapter
*/
final class RabbitMQAdapterTest extends TestCase
{
    private string $host;
    private int $port;
    private string $user;
    private string $pass;
    private string $vhost;
    private RabbitMQAdapter $adapter;

    protected function setUp(): void
    {
        $host = $_ENV['RABBITMQ_HOST'] ?? '127.0.0.1';
        $port = $_ENV['RABBITMQ_PORT'] ?? 5672;
        $user = $_ENV['RABBITMQ_USER'] ?? 'guest';
        $pass = $_ENV['RABBITMQ_PASS'] ?? 'guest';
        $vhost = $_ENV['RABBITMQ_VHOST'] ?? '/';

        $this->host = is_string($host)  ? $host  : '127.0.0.1';
        $this->port = is_numeric($port) ? (int) $port : 5672;
        $this->user = is_string($user)  ? $user  : 'guest';
        $this->pass = is_string($pass)  ? $pass  : 'guest';
        $this->vhost = is_string($vhost) ? $vhost : '/';

        $this->adapter = new RabbitMQAdapter(true, $this->host, $this->port, $this->user, $this->pass, $this->vhost);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        self::assertTrue($this->adapter->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $adapter = new RabbitMQAdapter(false, $this->host, $this->port, $this->user, $this->pass, $this->vhost);

        self::assertFalse($adapter->isEnabled());
    }

    public function testIsConnectedReturnsTrueWhenRunning(): void
    {
        $this->skipIfNotConnected();
        self::assertTrue($this->adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenUnreachable(): void
    {
        $adapter = new RabbitMQAdapter(true, '127.0.0.1', 19999, $this->user, $this->pass, $this->vhost);

        self::assertFalse($adapter->isConnected());
    }

    public function testGetMessengerDsnReturnsAmqpDsnWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        self::assertStringStartsWith('amqp://', $this->adapter->getMessengerDsn());
    }

    public function testGetMessengerDsnReturnsFallbackDsnWhenDisabled(): void
    {
        $adapter = new RabbitMQAdapter(false, $this->host, $this->port, $this->user, $this->pass, $this->vhost);

        self::assertStringStartsWith('doctrine://', $adapter->getMessengerDsn());
    }

    public function testGetConnectionDsnReturnsAmqpDsn(): void
    {
        $this->skipIfNotConnected();
        self::assertStringStartsWith('amqp://', $this->adapter->getConnectionDsn());
    }

    public function testGetConnectionDsnContainsHost(): void
    {
        $this->skipIfNotConnected();
        self::assertStringContainsString($this->host, $this->adapter->getConnectionDsn());
    }

    public function testGetConnectionDsnContainsPort(): void
    {
        $this->skipIfNotConnected();
        self::assertStringContainsString((string) $this->port, $this->adapter->getConnectionDsn());
    }

    public function testGetConnectionDsnContainsCredentials(): void
    {
        $this->skipIfNotConnected();
        self::assertStringContainsString($this->user, $this->adapter->getConnectionDsn());
    }

    public function testGetMessengerDsnAndConnectionDsnAreConsistentWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        self::assertSame($this->adapter->getConnectionDsn(), $this->adapter->getMessengerDsn());
    }

    private function skipIfNotConnected(): void
    {
        if (!$this->adapter->isConnected()) {
            self::markTestSkipped('RabbitMQ is not available.');
        }
    }
}
