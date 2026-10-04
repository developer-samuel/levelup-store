<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Order\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Order\Handler\Query\AdminApiOrderHistoryListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiOrderHistoryQueryController extends AbstractAdminApiQueryController
{
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

    #[OA\Get(
        path: '/api/admin/orders/history/list',
        summary: 'List order history',
        tags: ['Admin - Orders'],
        responses: [
            new OA\Response(response: 200, description: 'Order history list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(): JsonResponse
    {
        $orders = $this->orderHistoryListQueryHandler->handle();

        return $this->respondWithList($orders, 'orders');
    }
}
