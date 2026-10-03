<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\Banner;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\Banner\Handler\AdminApiBannerListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiBannerQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiBannerListQueryHandler $bannerListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly AdminApiBannerListQueryHandler $bannerListQueryHandler,
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
        path: '/api/admin/banners/list',
        summary: 'List all banners',
        tags: ['Admin - Banners'],
        responses: [
            new OA\Response(response: 200, description: 'Banners list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(): JsonResponse
    {
        $banners = $this->bannerListQueryHandler->handle();

        return $this->respondWithList($banners, 'banners');
    }
}
