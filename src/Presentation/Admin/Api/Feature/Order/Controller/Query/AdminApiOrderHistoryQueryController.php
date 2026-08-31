<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Feature\Order\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Admin\Api\Order\Handler\Query\AdminApiOrderHistoryListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminApiOrderHistoryQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiOrderHistoryListQueryHandler $orderHistoryListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly AdminApiOrderHistoryListQueryHandler $orderHistoryListQueryHandler,
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
     * @return JsonResponse
    */
    public function list(): JsonResponse
    {
        $orders = $this->orderHistoryListQueryHandler->handle();

        return $this->respondWithList($orders, 'orders');
    }
}
