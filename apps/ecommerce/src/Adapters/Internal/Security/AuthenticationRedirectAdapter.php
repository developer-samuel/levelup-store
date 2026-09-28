<?php

declare(strict_types=1);

namespace App\Adapters\Internal\Security;

use Symfony\{
    Component\HttpFoundation\RedirectResponse,
    Component\Routing\RouterInterface
};

use App\Core\Domain\Auth\Enum\AuthenticationRedirect;

use App\Core\Ports\Gateways\Internal\Security\AuthenticationRedirectGatewayContract;

final readonly class AuthenticationRedirectAdapter implements AuthenticationRedirectGatewayContract
{
    /**
     * @param RouterInterface $router
    */
    public function __construct(
        private RouterInterface $router,
    ) {}

    /**
     * @param AuthenticationRedirect $redirect
     *
     * @return RedirectResponse
    */
    public function redirectTo(AuthenticationRedirect $redirect): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate(match ($redirect) {
                AuthenticationRedirect::HOME            => 'home',
                AuthenticationRedirect::ADMIN_DASHBOARD => 'admin',
            }),
        );
    }
}
