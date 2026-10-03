<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Proxy;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SessionProxyContract
{
    public function get(): SessionInterface;
    public function invalidate(): void;
}
