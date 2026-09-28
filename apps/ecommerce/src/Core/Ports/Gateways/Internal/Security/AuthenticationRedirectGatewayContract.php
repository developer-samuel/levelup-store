<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\Internal\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Core\Domain\Auth\Enum\AuthenticationRedirect;

interface AuthenticationRedirectGatewayContract
{
    /**
     * @param AuthenticationRedirect $redirect
     *
     * @return RedirectResponse
    */
    public function redirectTo(AuthenticationRedirect $redirect): RedirectResponse;
}
