<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Proxy;

use Symfony\{
    Component\HttpFoundation\RequestStack,
    Component\HttpFoundation\Session\SessionInterface
};

use App\Core\Ports\Shared\Proxy\SessionProxyContract;

final readonly class SessionProxy implements SessionProxyContract
{
    /**
     * @param RequestStack $requestStack
    */
    public function __construct(
        private RequestStack $requestStack,
    ) {}

    /**
     * @return SessionInterface
    */
    public function get(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    /**
     * @return void
    */
    public function invalidate(): void
    {
        $this->get()->invalidate();
    }
}
