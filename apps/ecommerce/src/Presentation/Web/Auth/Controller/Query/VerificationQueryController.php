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

use App\Shared\Responder\ErrorResponder;

final class VerificationQueryController extends AbstractQueryController
{
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

    public function show(): Response
    {
        $user = $this->securityProvider->getCurrentUser();

        if ($user === null) {
            return $this->errorResponder->renderUnauthorized();
        }

        return $this->renderPage('features/auth/verification/must-verify.html.twig');
    }
}
