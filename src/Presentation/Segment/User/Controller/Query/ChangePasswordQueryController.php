<?php

declare(strict_types=1);

namespace App\Presentation\Segment\User\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

final class ChangePasswordQueryController extends AbstractQueryController
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @return Response
    */
    public function show(): Response
    {
        $user = $this->securityProvider->getCurrentUser();

        return $this->render('features/user/password-change/change-password.html.twig', [
            'user' => $user,
        ]);
    }
}
