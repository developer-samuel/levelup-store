<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Dashboard\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Application\Admin\Segment\Dashboard\Handler\Query\AdminDashboardQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminDashboardQueryController extends AbstractQueryController
{
    /**
     * @param AdminDashboardQueryHandler $adminDashboardQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private AdminDashboardQueryHandler $adminDashboardQueryHandler,
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
        $data = $this->adminDashboardQueryHandler->handle();

        return $this->renderPage('features/admin/views/dashboard/index.html.twig', $data);
    }
}
