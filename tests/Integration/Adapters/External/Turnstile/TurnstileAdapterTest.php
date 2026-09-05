<?php

declare(strict_types=1);

namespace Tests\Integration\Adapters\External\Turnstile;

use PHPUnit\Framework\TestCase;

use Symfony\Component\HttpClient\HttpClient;

use App\Core\Ports\Gateways\External\Turnstile\TurnstileGatewayContract;

use App\Adapters\External\Turnstile\TurnstileAdapter;

/**
 * @coversDefaultClass \App\Adapters\External\Turnstile\TurnstileAdapter
*/
class TurnstileAdapterTest extends TestCase
{
    private string $secretKey;
    private string $verifyUrl;
    private TurnstileAdapter $adapter;

    protected function setUp(): void
    {
        $secretKey = $_ENV['TURNSTILE_SECRET_KEY'] ?? '';
        $verifyUrl = $_ENV['TURNSTILE_VERIFY_URL'] ?? 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        $this->secretKey = is_string($secretKey) ? $secretKey : '';
        $this->verifyUrl = is_string($verifyUrl)  ? $verifyUrl  : 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        $this->adapter = new TurnstileAdapter(
            HttpClient::create(),
            true,
            $this->secretKey,
            $this->verifyUrl,
        );
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(TurnstileGatewayContract::class, $this->adapter);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $this->assertTrue($this->adapter->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $adapter = new TurnstileAdapter(HttpClient::create(), false, $this->secretKey, $this->verifyUrl);

        $this->assertFalse($adapter->isEnabled());
    }

    public function testVerifyReturnsTrueWhenDisabled(): void
    {
        $adapter = new TurnstileAdapter(HttpClient::create(), false, $this->secretKey, $this->verifyUrl);

        $this->assertTrue($adapter->verify('any-token', '127.0.0.1'));
    }

    public function testVerifyReturnsFalseOnEmptyToken(): void
    {
        $this->assertFalse($this->adapter->verify('', '127.0.0.1'));
    }

    public function testVerifyReturnsTrueWithTestSecretKey(): void
    {
        $adapter = new TurnstileAdapter(
            HttpClient::create(),
            true,
            '1x0000000000000000000000000000000AA',
            $this->verifyUrl,
        );

        $this->assertTrue($adapter->verify('test-token', '127.0.0.1'));
    }

    public function testVerifyReturnsFalseOnInvalidToken(): void
    {
        $adapter = new TurnstileAdapter(
            HttpClient::create(),
            true,
            'fake-secret-key',
            $this->verifyUrl,
        );

        $this->assertFalse($adapter->verify('invalid-token', '127.0.0.1'));
    }

    public function testVerifyReturnsFalseOnUnreachableUrl(): void
    {
        $adapter = new TurnstileAdapter(
            HttpClient::create(),
            true,
            $this->secretKey,
            'http://127.0.0.1:19999/turnstile',
        );

        $this->assertFalse($adapter->verify('some-token', '127.0.0.1'));
    }
}
