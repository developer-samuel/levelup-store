<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Product\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Product\Handler\Query\AdminApiProductSubtypeListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiProductSubtypeQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiProductSubtypeListQueryHandler $subtypeListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly AdminApiProductSubtypeListQueryHandler $subtypeListQueryHandler,
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
        path: '/api/admin/products/subtypes/list/{id}',
        summary: 'List product subtypes for a given product',
        tags: ['Admin - Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Subtypes list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(int $id): JsonResponse
    {
        $subtypes = $this->subtypeListQueryHandler->handle(['id' => $id]);

        return $this->respondWithList($subtypes, 'subtypes');
    }
}
