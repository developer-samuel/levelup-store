<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Auth\Controller\Command;

use Symfony\{
    Bundle\FrameworkBundle\KernelBrowser,
    Bundle\FrameworkBundle\Test\WebTestCase
};

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Ports\{
    Auth\Handler\Command\LoginHandlerContract,
    Auth\Handler\Command\LogoutHandlerContract,
    Auth\Handler\Command\RefreshTokenHandlerContract
};

use Tests\Support\{
    Traits\RateLimiterMockTrait,
    Provides\DecodesJson
};

/**
 * @coversDefaultClass \App\Presentation\Auth\Controller\Command\AuthApiCommandController
*/
final class AuthApiCommandControllerTest extends WebTestCase
{
    use DecodesJson;
    use RateLimiterMockTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        static::getContainer()->set('App\Infrastructure\RateLimiter\LoginRateLimiter', $this->createRateLimiterMock());
    }

    public function testLoginReturnsSuccessJsonOnValidCredentials(): void
    {
        $this->postLogin([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertSame('success', $this->decodeJson()['status']);
    }

    public function testLoginSetsRefreshTokenCookie(): void
    {
        $this->postLogin([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
        ]);

        self::assertNotNull($this->client->getCookieJar()->get('refresh_token'));
    }

    public function testLoginDoesNotReturnRefreshTokenInBody(): void
    {
        $this->postLogin([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
        ]);

        self::assertArrayNotHasKey('refresh_token', $this->decodeJson());
    }

    public function testLoginReturnsUnprocessableOnInvalidPayload(): void
    {
        $this->postJson('/api/auth/login', ['email' => 'not-an-email', 'password' => '']);

        self::assertResponseStatusCodeSame(422);

        $data = $this->decodeJson();

        self::assertFalse($data['success']);
        self::assertNotEmpty($data['errors']);
    }

    public function testLoginReturnsSuccessResponseBody(): void
    {
        $this->postLogin([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
        ]);

        self::assertArrayHasKey('access_token', $this->decodeJson());
    }

    public function testRefreshReturnsSuccessJson(): void
    {
        $this->postRefresh([
            'status'        => 'success',
            'access_token'  => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame('success', $this->decodeJson()['status']);
    }

    public function testRefreshSetsNewRefreshTokenCookie(): void
    {
        $this->postRefresh([
            'status'        => 'success',
            'access_token'  => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
        ]);

        self::assertResponseIsSuccessful();
        self::assertNotNull($this->client->getCookieJar()->get('refresh_token'));
    }

    public function testRefreshWithEmptyCookieSendsNullToken(): void
    {
        $handler = $this->createMock(RefreshTokenHandlerContract::class);
        $handler->expects(self::once())
            ->method('handle')
            ->with(null)
            ->willReturn(['status' => 'success', 'access_token' => 'tok', 'refresh_token' => 'r']);

        static::getContainer()->set(RefreshTokenHandlerContract::class, $handler);

        $this->client->request('POST', '/api/auth/refresh');

        self::assertResponseIsSuccessful();
    }

    public function testLogoutReturnsSuccessJson(): void
    {
        $this->postLogout(['status' => 'success', 'message' => 'Logged out successfully']);

        self::assertResponseIsSuccessful();
        self::assertSame('success', $this->decodeJson()['status']);
    }

    public function testLogoutClearsRefreshTokenCookie(): void
    {
        $this->postLogout(['status' => 'success', 'message' => 'Logged out']);

        $setCookie = $this->client->getResponse()->headers->get('Set-Cookie');

        self::assertNotNull($setCookie);
        self::assertStringContainsString('refresh_token', $setCookie);
    }

    public function testLogoutBodyContainsSuccessTrue(): void
    {
        $this->postLogout(['status' => 'success', 'message' => 'Logged out']);

        self::assertTrue($this->decodeJson()['success']);
    }

    public function testLogoutWithEmptyCookieSendsNullToken(): void
    {
        $handler = $this->createMock(LogoutHandlerContract::class);
        $handler->expects(self::once())
            ->method('handle')
            ->with(null)
            ->willReturn(['status' => 'success', 'message' => 'ok']);

        static::getContainer()->set(LogoutHandlerContract::class, $handler);

        $this->client->request('POST', '/api/auth/logout');

        self::assertResponseIsSuccessful();
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function postLogin(array $returnValue): void
    {
        static::getContainer()->set(
            LoginHandlerContract::class,
            $this->createLoginHandlerMock($returnValue),
        );

        $this->postJson('/api/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'Password1!',
        ]);
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function postRefresh(array $returnValue): void
    {
        static::getContainer()->set(
            RefreshTokenHandlerContract::class,
            $this->createRefreshHandlerMock($returnValue),
        );

        $this->client->request('POST', '/api/auth/refresh', [], [], [
            'HTTP_COOKIE' => 'refresh_token=existing-refresh-token',
        ]);
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function postLogout(array $returnValue): void
    {
        static::getContainer()->set(
            LogoutHandlerContract::class,
            $this->createLogoutHandlerMock($returnValue),
        );

        $this->client->request('POST', '/api/auth/logout', [], [], [
            'HTTP_COOKIE' => 'refresh_token=some-token',
        ]);
    }

    /**
     * @param array<string, mixed> $payload
    */
    private function postJson(string $uri, array $payload): void
    {
        $this->client->request('POST', $uri, [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) json_encode($payload));
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function createLoginHandlerMock(array $returnValue): LoginHandlerContract&MockObject
    {
        $handler = $this->createMock(LoginHandlerContract::class);
        $handler->method('handle')->willReturn($returnValue);

        return $handler;
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function createRefreshHandlerMock(array $returnValue): RefreshTokenHandlerContract&MockObject
    {
        $handler = $this->createMock(RefreshTokenHandlerContract::class);
        $handler->method('handle')->willReturn($returnValue);

        return $handler;
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function createLogoutHandlerMock(array $returnValue): LogoutHandlerContract&MockObject
    {
        $handler = $this->createMock(LogoutHandlerContract::class);
        $handler->method('handle')->willReturn($returnValue);

        return $handler;
    }
}
