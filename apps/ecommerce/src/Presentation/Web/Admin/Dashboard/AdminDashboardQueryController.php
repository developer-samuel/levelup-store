<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Dashboard;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Application\Admin\Segment\Dashboard\Handler\AdminDashboardQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class AdminDashboardQueryController extends AbstractQueryController
{
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

    public function index(): Response
    {
        $data = $this->adminDashboardQueryHandler->handle();

        return $this->renderPage('features/admin/views/dashboard/index.html.twig', $data);
    }
}
