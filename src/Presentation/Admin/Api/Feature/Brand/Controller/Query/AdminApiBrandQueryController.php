<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Feature\Brand\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Admin\Api\Brand\Handler\Query\AdminApiBrandListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminApiBrandQueryController extends AbstractAdminApiQueryController
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

    /**
     * @return JsonResponse
    */
    public function list(): JsonResponse
    {
        $brands = $this->brandListQueryHandler->handle();

        return $this->respondWithList($brands, 'brands');
    }
}
