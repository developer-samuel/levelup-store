<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Product\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Product\Handler\Query\AdminApiProductListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiProductQueryController extends AbstractAdminApiQueryController
{
    public function __construct(
        private readonly AdminApiProductListQueryHandler $productListQueryHandler,
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
        path: '/api/admin/products/list',
        summary: 'List all products',
        tags: ['Admin - Products'],
        responses: [
            new OA\Response(response: 200, description: 'Products list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(): JsonResponse
    {
        $products = $this->productListQueryHandler->handle();

        return $this->respondWithList($products, 'products');
    }
}
