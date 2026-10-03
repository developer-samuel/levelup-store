<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Manager;

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

    public function __construct(
        private CookieGatewayContract $cookieGateway,
        private int $refreshTokenTtl,
    ) {}

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

    /** @param array<string, mixed> $result */
    public function attach(array &$result, JsonResponse $response, bool $secure): void
    {
        $raw = $result['refresh_token'] ?? null;
        unset($result['refresh_token']);

        if (is_string($raw)) {
            $response->headers->setCookie($this->create($raw, $secure));
        }
    }

    public function clear(ResponseHeaderBag $headers, bool $secure): void
    {
        $headers->clearCookie(self::COOKIE_NAME, '/', null, $secure, true);
    }
}
