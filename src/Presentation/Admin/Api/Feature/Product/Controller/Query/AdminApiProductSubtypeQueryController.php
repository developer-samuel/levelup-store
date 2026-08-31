<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Feature\Product\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Admin\Api\Product\Handler\Query\AdminApiProductSubtypeListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminApiProductSubtypeQueryController extends AbstractAdminApiQueryController
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

    /**
     * @param int $id
     *
     * @return JsonResponse
    */
    public function list(int $id): JsonResponse
    {
        $subtypes = $this->subtypeListQueryHandler->handle(['id' => $id]);

        return $this->respondWithList($subtypes, 'subtypes');
    }
}
