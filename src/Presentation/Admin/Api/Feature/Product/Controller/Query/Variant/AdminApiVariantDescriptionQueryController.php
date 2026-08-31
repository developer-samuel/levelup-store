<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Feature\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Admin\Api\Product\Handler\Query\Variant\AdminApiVariantDescriptionListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminApiVariantDescriptionQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiVariantDescriptionListQueryHandler $descriptionListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly AdminApiVariantDescriptionListQueryHandler $descriptionListQueryHandler,
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
        $descriptions = $this->descriptionListQueryHandler->handle(['variantId' => $id]);

        return $this->respondWithList($descriptions, 'descriptions');
    }
}
