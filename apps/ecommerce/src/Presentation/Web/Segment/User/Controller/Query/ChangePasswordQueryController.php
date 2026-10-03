<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\User\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class ChangePasswordQueryController extends AbstractQueryController
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

    public function show(): Response
    {
        $user = $this->securityProvider->getCurrentUser();

        return $this->render('features/user/password-change/change-password.html.twig', [
            'user' => $user,
        ]);
    }
}
