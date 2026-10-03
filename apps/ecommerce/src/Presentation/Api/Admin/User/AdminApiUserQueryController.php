<?php

declare(strict_types=1);

namespace App\Presentation\Api\Admin\User;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Application\Admin\Api\User\Handler\AdminApiUserListQueryHandler;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Api\Admin\Abstract\AbstractAdminApiQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminApiUserQueryController extends AbstractAdminApiQueryController
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

    #[OA\Get(
        path: '/api/admin/users/list',
        summary: 'List all users',
        tags: ['Admin - Users'],
        responses: [
            new OA\Response(response: 200, description: 'Users list'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Insufficient permissions'),
            new OA\Response(response: 404, description: 'Resource not found'),
        ],
    )]
    public function list(): JsonResponse
    {
        $users = $this->userListQueryHandler->handle();

        return $this->respondWithList($users, 'users');
    }
}
