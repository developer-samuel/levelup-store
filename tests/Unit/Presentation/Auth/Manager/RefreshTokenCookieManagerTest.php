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

        self::assertSame($cookie, $result);
    }

    public function testCreatePassesCookieObjectToGateway(): void
    {
        $this->cookieGateway
            ->expects(self::once())
            ->method('apply')
            ->with(self::isInstanceOf(CookieObject::class))
            ->willReturn($this->buildCookie());

        $this->manager->create('token-value', false);
    }

    public function testAttachSetsCookieOnResponseWhenTokenPresent(): void
    {
        [$result, $response] = $this->performAttachWithToken();

        self::assertNotEmpty($response->headers->getCookies());
    }

    public function testAttachRemovesRefreshTokenFromResult(): void
    {
        [$result] = $this->performAttachWithToken();

        self::assertArrayNotHasKey('refresh_token', $result);
    }

    public function testAttachDoesNotSetCookieWhenTokenMissing(): void
    {
        $this->cookieGateway->expects(self::never())->method('apply');

        $result = ['access_token' => 'access-xyz'];
        $response = new JsonResponse($result);

        $this->manager->attach($result, $response, false);

        self::assertEmpty($response->headers->getCookies());
    }

    public function testAttachDoesNotSetCookieWhenTokenIsNotString(): void
    {
        $this->cookieGateway->expects(self::never())->method('apply');

        $result = ['refresh_token' => 123, 'access_token' => 'access-xyz'];
        $response = new JsonResponse($result);

        $this->manager->attach($result, $response, false);

        self::assertEmpty($response->headers->getCookies());
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

        self::assertNotEmpty($cleared);
    }

    public function testClearSetsCookieNameRefreshToken(): void
    {
        $headers = new ResponseHeaderBag();

        $this->manager->clear($headers, true);

        $cookies = $headers->getCookies();
        self::assertNotEmpty($cookies);
        self::assertSame('refresh_token', $cookies[0]->getName());
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
