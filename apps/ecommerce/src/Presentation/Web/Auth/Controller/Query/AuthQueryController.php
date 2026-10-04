<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class AuthQueryController extends AbstractQueryController
{
    public function __construct(
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

    public function login(): Response
    {
        return $this->render('features/auth/login/login.html.twig');
    }
    
    public function signup(): Response
    {
        return $this->renderPage('features/auth/signup/signup.html.twig');
    }
}
