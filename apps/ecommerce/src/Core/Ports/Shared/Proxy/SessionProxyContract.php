<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Proxy;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SessionProxyContract
{
    /**
     * @return SessionInterface
    */
    public function get(): SessionInterface;

    /**
     * @return void
    */
    public function invalidate(): void;
}
