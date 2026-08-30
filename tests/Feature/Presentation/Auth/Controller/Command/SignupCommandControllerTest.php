<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Auth\Controller\Command;

use Symfony\{
    Bundle\FrameworkBundle\KernelBrowser,
    Bundle\FrameworkBundle\Test\WebTestCase
};

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Ports\Auth\Handler\Command\SignupHandlerContract;

use Tests\Support\{
    Traits\RateLimiterMockTrait,
    Provides\DecodesJson
};

/**
 * @coversDefaultClass \App\Presentation\Auth\Controller\Command\SignupCommandController
*/
class SignupCommandControllerTest extends WebTestCase
{
    use DecodesJson;
    use RateLimiterMockTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        static::getContainer()->set('App\Infrastructure\RateLimiter\SignupRateLimiter', $this->createRateLimiterMock());
    }

    public function testStoreReturnsSuccessJson(): void
    {
        $this->setHandlerMock([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
            'redirect'      => '/',
        ]);

        $this->client->request('POST', '/signup/store', $this->buildPayload());

        $this->assertResponseIsSuccessful();
        $this->assertTrue($this->decodeJson()['success']);
    }

    public function testStoreSetsRefreshTokenCookie(): void
    {
        $this->setHandlerMock([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
            'redirect'      => '/',
        ]);

        $this->client->request('POST', '/signup/store', $this->buildPayload());

        $this->assertNotNull($this->client->getCookieJar()->get('refresh_token'));
    }

    public function testStoreDoesNotReturnRefreshTokenInBody(): void
    {
        $this->setHandlerMock([
            'status'        => 'success',
            'access_token'  => 'access-token-abc',
            'refresh_token' => 'refresh-token-xyz',
            'redirect'      => '/',
        ]);

        $this->client->request('POST', '/signup/store', $this->buildPayload());

        $this->assertArrayNotHasKey('refresh_token', $this->decodeJson());
    }

    public function testStoreReturnsJsonResponse(): void
    {
        $this->setHandlerMock([
            'status'        => 'success',
            'access_token'  => 'at',
            'refresh_token' => 'rt',
            'redirect'      => '/',
        ]);

        $this->client->request('POST', '/signup/store', $this->buildPayload());

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testStoreReturnsUnprocessableOnValidationErrors(): void
    {
        $this->client->request('POST', '/signup/store', []);

        $this->assertResponseStatusCodeSame(422);

        $data = $this->decodeJson();

        $this->assertFalse($data['success']);
        $this->assertNotEmpty($data['errors']);
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function setHandlerMock(array $returnValue): void
    {
        static::getContainer()->set(
            SignupHandlerContract::class,
            $this->createSignupHandlerMock($returnValue),
        );
    }

    /**
     * @return array<string, mixed>
    */
    private function buildPayload(): array
    {
        return [
            'email'                 => 'signup-' . uniqid() . '@test.com',
            'first_name'            => 'John',
            'last_name'             => 'Doe',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms_and_conditions'  => '1',
        ];
    }

    /**
     * @param array<string, mixed> $returnValue
    */
    private function createSignupHandlerMock(array $returnValue): SignupHandlerContract&MockObject
    {
        $handler = $this->createMock(SignupHandlerContract::class);
        $handler->method('handle')->willReturn($returnValue);

        return $handler;
    }
}
