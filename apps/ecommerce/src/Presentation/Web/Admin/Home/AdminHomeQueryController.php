<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Home;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class AdminHomeQueryController extends AbstractQueryController
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
    public function index(): Response
    {
        return $this->renderPage('features/admin/views/home/index.html.twig');
    }
}
