<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Auth\Manager;

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use Symfony\Component\HttpFoundation\{
    Cookie,
    JsonResponse,
    ResponseHeaderBag
};

use App\Core\Domain\Cookie\CookieObject;

use App\Core\Ports\Gateways\Internal\Cookie\CookieGatewayContract;

use App\Presentation\Auth\Manager\RefreshTokenCookieManager;

/**
 * @coversDefaultClass \App\Presentation\Auth\Manager\RefreshTokenCookieManager
*/
final class RefreshTokenCookieManagerTest extends TestCase
{
    private CookieGatewayContract&MockObject $cookieGateway;
    private RefreshTokenCookieManager $manager;

    protected function setUp(): void
    {
        $this->cookieGateway = $this->createMock(CookieGatewayContract::class);
        $this->manager = new RefreshTokenCookieManager($this->cookieGateway, 3600);
    }

    public function testCreateReturnsCookieFromGateway(): void
    {
        $cookie = $this->buildCookie();

        $this->cookieGateway->method('apply')->willReturn($cookie);

        $result = $this->manager->create('token-value', false);

        $this->assertSame($cookie, $result);
    }

    public function testCreatePassesCookieObjectToGateway(): void
    {
        $this->cookieGateway
            ->expects($this->once())
            ->method('apply')
            ->with($this->isInstanceOf(CookieObject::class))
            ->willReturn($this->buildCookie());

        $this->manager->create('token-value', false);
    }

    public function testAttachSetsCookieOnResponseWhenTokenPresent(): void
    {
        [$result, $response] = $this->performAttachWithToken();

        $this->assertNotEmpty($response->headers->getCookies());
    }

    public function testAttachRemovesRefreshTokenFromResult(): void
    {
        [$result] = $this->performAttachWithToken();

        $this->assertArrayNotHasKey('refresh_token', $result);
    }

    public function testAttachDoesNotSetCookieWhenTokenMissing(): void
    {
        $this->cookieGateway->expects($this->never())->method('apply');

        $result = ['access_token' => 'access-xyz'];
        $response = new JsonResponse($result);

        $this->manager->attach($result, $response, false);

        $this->assertEmpty($response->headers->getCookies());
    }

    public function testAttachDoesNotSetCookieWhenTokenIsNotString(): void
    {
        $this->cookieGateway->expects($this->never())->method('apply');

        $result = ['refresh_token' => 123, 'access_token' => 'access-xyz'];
        $response = new JsonResponse($result);

        $this->manager->attach($result, $response, false);

        $this->assertEmpty($response->headers->getCookies());
    }

    public function testClearRemovesCookieFromHeaders(): void
    {
        $headers = new ResponseHeaderBag();

        $this->manager->clear($headers, false);

        $cookies = $headers->getCookies();
        $cleared = array_filter(
            $cookies,
            fn(Cookie $c): bool => $c->getName() === 'refresh_token' && $c->isCleared(),
        );

        $this->assertNotEmpty($cleared);
    }

    public function testClearSetsCookieNameRefreshToken(): void
    {
        $headers = new ResponseHeaderBag();

        $this->manager->clear($headers, true);

        $cookies = $headers->getCookies();
        $this->assertNotEmpty($cookies);
        $this->assertSame('refresh_token', $cookies[0]->getName());
    }

    /**
     * @return array{0: array<string, mixed>, 1: JsonResponse}
    */
    private function performAttachWithToken(): array
    {
        $this->cookieGateway->method('apply')->willReturn($this->buildCookie());

        $result = ['refresh_token' => 'token-abc', 'access_token' => 'access-xyz'];
        $response = new JsonResponse($result);

        $this->manager->attach($result, $response, false);

        return [$result, $response];
    }

    private function buildCookie(): Cookie
    {
        return Cookie::create('refresh_token', 'token-value');
    }
}
