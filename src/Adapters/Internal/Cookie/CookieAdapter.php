<?php

declare(strict_types=1);

namespace App\Adapters\Internal\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

use App\Core\Domain\Cookie\CookieObject;

use App\Core\Ports\Gateways\Internal\Cookie\CookieGatewayContract;

final class CookieAdapter implements CookieGatewayContract
{
    /**
     * @param CookieObject $cookie
     *
     * @return Cookie
    */
    public function apply(CookieObject $cookie): Cookie
    {
        /** @var ''|'lax'|'none'|'strict' $sameSite */
        $sameSite = $cookie->sameSite ?? Cookie::SAMESITE_LAX;

        return Cookie::create(
            name: $cookie->name,
            value: $cookie->value,
            expire: $cookie->expiresAt,
            path: $cookie->path,
            secure: $cookie->secure,
            httpOnly: $cookie->httpOnly,
            sameSite: $sameSite,
        );
    }
}
