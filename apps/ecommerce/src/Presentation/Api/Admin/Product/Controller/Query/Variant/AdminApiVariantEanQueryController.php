<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Product\Handler\Query\Variant\AdminApiVariantEanListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiVariantEanQueryController extends AbstractAdminApiQueryController
{
    public function __construct(
        private readonly AdminApiVariantEanListQueryHandler $eanListQueryHandler,
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
        path: '/api/admin/variants/eans/list/{id}',
        summary: 'List variant EAN codes',
        tags: ['Admin - Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'EANs list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(int $id): JsonResponse
    {
        $eans = $this->eanListQueryHandler->handle(['variantId' => $id]);

        return $this->respondWithList($eans, 'eans');
    }
}
