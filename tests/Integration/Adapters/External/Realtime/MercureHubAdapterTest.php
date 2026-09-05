<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Realtime;

use PHPUnit\Framework\TestCase;

use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\Jwt\FactoryTokenProvider;
use Symfony\Component\Mercure\Jwt\LcobucciFactory;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;

use App\Core\Ports\Gateways\External\Realtime\MercureHubGatewayContract;

use App\Adapters\External\Realtime\MercureHubAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Realtime\MercureHubAdapter
*/
class MercureHubAdapterTest extends TestCase
{
    private string $hubUrl;
    private MercureHubAdapter $adapter;

    protected function setUp(): void
    {
        $hubUrl    = $_ENV['MERCURE_URL']        ?? 'http://127.0.0.1:9300/.well-known/mercure';
        $jwtSecret = $_ENV['MERCURE_JWT_SECRET'] ?? 'test-secret';

        $this->hubUrl = is_string($hubUrl) && $hubUrl !== '' ? $hubUrl : 'http://127.0.0.1:9300/.well-known/mercure';
        $jwtSecret    = is_string($jwtSecret) && $jwtSecret !== '' ? $jwtSecret : 'test-secret';

        $tokenProvider = new FactoryTokenProvider(
            new LcobucciFactory($jwtSecret),
            [],
            ['mercure' => ['publish' => ['*']]],
        );
        $hub = new Hub($this->hubUrl, $tokenProvider);

        $this->adapter = new MercureHubAdapter($hub, true, $this->hubUrl);
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(MercureHubGatewayContract::class, $this->adapter);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $this->assertTrue($this->adapter->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $hub     = new Hub($this->hubUrl, new StaticTokenProvider('test'));
        $adapter = new MercureHubAdapter($hub, false, $this->hubUrl);

        $this->assertFalse($adapter->isEnabled());
    }

    public function testIsConnectedReturnsFalseWhenDisabled(): void
    {
        $hub     = new Hub($this->hubUrl, new StaticTokenProvider('test'));
        $adapter = new MercureHubAdapter($hub, false, $this->hubUrl);

        $this->assertFalse($adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenUnreachable(): void
    {
        $hub     = new Hub('http://127.0.0.1:19999/.well-known/mercure', new StaticTokenProvider('test'));
        $adapter = new MercureHubAdapter($hub, true, 'http://127.0.0.1:19999/.well-known/mercure');

        $this->assertFalse($adapter->isConnected());
    }

    public function testIsConnectedReturnsFalseWhenUrlIsMalformed(): void
    {
        $hub     = new Hub($this->hubUrl, new StaticTokenProvider('test'));
        $adapter = new MercureHubAdapter($hub, true, 'not-a-valid-url');

        $this->assertFalse($adapter->isConnected());
    }

    public function testIsConnectedReturnsTrueWhenRunning(): void
    {
        $this->skipIfNotConnected();
        $this->assertTrue($this->adapter->isConnected());
    }

    public function testPublishDoesNothingWhenDisabled(): void
    {
        $this->expectNotToPerformAssertions();

        $hub     = new Hub($this->hubUrl, new StaticTokenProvider('test'));
        $adapter = new MercureHubAdapter($hub, false, $this->hubUrl);

        $adapter->publish('https://example.com/topic', '{"test":true}');
    }

    public function testPublishSendsUpdateWhenEnabled(): void
    {
        $this->skipIfNotConnected();
        $this->expectNotToPerformAssertions();

        $this->adapter->publish('https://example.com/topic', '{"test":true}');
    }

    private function skipIfNotConnected(): void
    {
        if (!$this->adapter->isConnected()) {
            $this->markTestSkipped('Mercure is not available.');
        }
    }
}
