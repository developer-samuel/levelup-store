<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters\Internal\Security;

use Symfony\{
    Component\HttpFoundation\RedirectResponse,
    Component\Routing\RouterInterface
};

use PHPUnit\{
    Framework\MockObject\MockObject,
    Framework\TestCase
};

use App\Core\Domain\Auth\Enum\AuthenticationRedirect;

use App\Core\Ports\Gateways\Internal\Security\AuthenticationRedirectGatewayContract;

use App\Adapters\Internal\Security\AuthenticationRedirectAdapter;

/**
 * @coversDefaultClass \App\Adapters\Internal\Security\AuthenticationRedirectAdapter
*/
final class AuthenticationRedirectAdapterTest extends TestCase
{
    private RouterInterface&MockObject $router;
    private AuthenticationRedirectAdapter $adapter;

    protected function setUp(): void
    {
        $this->initMocks();
        $this->initAdapter();
    }

    public function testImplementsContract(): void
    {
        $this->assertInstanceOf(AuthenticationRedirectGatewayContract::class, $this->adapter);
    }

    public function testRedirectToHomeGeneratesHomeRoute(): void
    {
        $result = $this->redirectTo(AuthenticationRedirect::HOME, 'home', '/');

        $this->assertSame('/', $result->getTargetUrl());
    }

    public function testRedirectToAdminDashboardGeneratesAdminRoute(): void
    {
        $result = $this->redirectTo(AuthenticationRedirect::ADMIN_DASHBOARD, 'admin', '/admin');

        $this->assertSame('/admin', $result->getTargetUrl());
    }

    private function initMocks(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
    }

    private function initAdapter(): void
    {
        $this->adapter = new AuthenticationRedirectAdapter($this->router);
    }

    private function redirectTo(AuthenticationRedirect $redirect, string $route, string $url): RedirectResponse
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with($route)
            ->willReturn($url);

        $result = $this->adapter->redirectTo($redirect);

        $this->assertInstanceOf(RedirectResponse::class, $result);

        return $result;
    }
}
