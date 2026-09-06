<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

use App\Core\Domain\Cookie\CookieObject;

interface CookieGatewayContract
{
    /**
     * @param CookieObject $cookie
     *
     * @return Cookie
    */
    public function apply(CookieObject $cookie): Cookie;
}
