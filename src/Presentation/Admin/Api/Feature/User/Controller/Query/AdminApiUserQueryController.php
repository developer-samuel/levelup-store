<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Api\Feature\User\Controller\Query;

use Symfony\Component\HttpFoundation\JsonResponse;

use App\Core\Application\Admin\Api\User\Handler\Query\AdminApiUserListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Api\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

class AdminApiUserQueryController extends AbstractAdminApiQueryController
{
    /**
     * @param AdminApiUserListQueryHandler $userListQueryHandler
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly AdminApiUserListQueryHandler $userListQueryHandler,
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
        $users = $this->userListQueryHandler->handle();

        return $this->respondWithList($users, 'users');
    }
}
