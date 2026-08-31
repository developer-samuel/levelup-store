<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Feature\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Admin\Api\Product\Handler\Query\Variant\AdminApiVariantEanListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminApiVariantEanQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiVariantEanListQueryHandler $eanListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @param int $id
     *
     * @return JsonResponse
    */
    public function list(int $id): JsonResponse
    {
        $eans = $this->eanListQueryHandler->handle(['variantId' => $id]);

        return $this->respondWithList($eans, 'eans');
    }
}
