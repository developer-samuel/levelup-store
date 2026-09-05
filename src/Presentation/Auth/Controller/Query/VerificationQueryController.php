<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

use App\Shared\Responder\ErrorResponder;

final class VerificationQueryController extends AbstractQueryController
{
    /**
     * @param ErrorResponder $errorResponder
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ErrorResponder $errorResponder,
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
     * @return Response
    */
    public function show(): Response
    {
        $user = $this->securityProvider->getCurrentUser();

        if ($user === null) {
            return $this->errorResponder->renderUnauthorized();
        }

        return $this->renderPage('features/auth/verification/must-verify.html.twig');
    }
}
