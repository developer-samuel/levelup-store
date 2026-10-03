<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Brand;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Brand\Handler\AdminApiBrandListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiBrandQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiBrandListQueryHandler $brandListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly AdminApiBrandListQueryHandler $brandListQueryHandler,
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
        path: '/api/admin/brands/list',
        summary: 'List all brands',
        tags: ['Admin - Brands'],
        responses: [
            new OA\Response(response: 200, description: 'Brands list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(): JsonResponse
    {
        $brands = $this->brandListQueryHandler->handle();

        return $this->respondWithList($brands, 'brands');
    }
}
