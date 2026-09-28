<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Controller\Query;

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
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

final class ResetPasswordQueryController extends AbstractQueryController
{
    /**
     * @param ResetPasswordQueryContract $resetPasswordQuery
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @param string $token
     *
     * @return Response
    */
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
