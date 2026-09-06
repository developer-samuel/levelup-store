<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Manager;

use Symfony\{
    Component\HttpFoundation\Cookie,
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\ResponseHeaderBag
};

use App\Core\Domain\Cookie\CookieObject;

use App\Core\Ports\Gateways\Internal\Cookie\CookieGatewayContract;

final readonly class RefreshTokenCookieManager
{
    private const COOKIE_NAME = 'refresh_token';

    /**
     * @param CookieGatewayContract $cookieGateway
     * @param int $refreshTokenTtl
    */
    public function __construct(
        private CookieGatewayContract $cookieGateway,
        private int $refreshTokenTtl,
    ) {}

    /**
     * @param string $token
     * @param bool $secure
     *
     * @return Cookie
    */
    public function create(string $token, bool $secure): Cookie
    {
        $cookie = new CookieObject(
            name: self::COOKIE_NAME,
            value: $token,
            expiresAt: time() + $this->refreshTokenTtl,
            path: '/',
            secure: $secure,
            httpOnly: true,
            sameSite: Cookie::SAMESITE_LAX,
        );

        return $this->cookieGateway->apply($cookie);
    }

    /**
     * @param array<string, mixed> $result
     * @param JsonResponse $response
     * @param bool $secure
     *
     * @return void
    */
    public function attach(array &$result, JsonResponse $response, bool $secure): void
    {
        $raw = $result['refresh_token'] ?? null;
        unset($result['refresh_token']);

        if (is_string($raw)) {
            $response->headers->setCookie($this->create($raw, $secure));
        }
    }

    /**
     * @param ResponseHeaderBag $headers
     * @param bool $secure
     *
     * @return void
    */
    public function clear(ResponseHeaderBag $headers, bool $secure): void
    {
        $headers->clearCookie(self::COOKIE_NAME, '/', null, $secure, true);
    }
}
