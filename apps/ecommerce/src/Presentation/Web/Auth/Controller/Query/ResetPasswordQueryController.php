<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Controller\Query;

use Symfony\{
    Component\HttpFoundation\RedirectResponse,
    Component\HttpFoundation\Response
};

use App\Core\Ports\{
    Auth\Service\Query\ResetPasswordQueryContract,
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class ResetPasswordQueryController extends AbstractQueryController
{
    public function __construct(
        private readonly ResetPasswordQueryContract $resetPasswordQuery,
        SecurityProviderContract $securityProvider,
        ExceptionResponder $exceptionResponder,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityProvider,
            $exceptionResponder,
            $logger,
        );
    }

    public function show(string $token): Response
    {
        $tokenEntity = $this->resetPasswordQuery->getValidToken($token);

        if ($tokenEntity === null) {
            return new RedirectResponse('/forgot-password');
        }

        return $this->render('features/auth/password/reset/reset-password.html.twig', [
            'token' => $token,
        ]);
    }
}
