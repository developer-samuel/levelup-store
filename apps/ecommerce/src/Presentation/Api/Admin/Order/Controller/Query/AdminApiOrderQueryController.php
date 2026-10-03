<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Order\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Order\Handler\Query\AdminApiOrderListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiOrderQueryController extends AbstractAdminApiQueryController
{
    public function __construct(
        private readonly AdminApiOrderListQueryHandler $orderListQueryHandler,
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
        path: '/api/admin/orders/list',
        summary: 'List all orders',
        tags: ['Admin - Orders'],
        responses: [
            new OA\Response(response: 200, description: 'Orders list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(): JsonResponse
    {
        $orders = $this->orderListQueryHandler->handle();

        return $this->respondWithList($orders, 'orders');
    }
}
