<?php

declare(strict_types=1);

namespace App\Presentation\Api\Assistant;

use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCommandController,
    Shared\Responder\HttpResponder
};

final class AssistantApiQueryController extends AbstractCommandController
{
    public function __construct(
        private readonly SecurityProviderContract $securityProvider,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    #[OA\Get(
        path: '/api/assistant/session',
        summary: 'Get current assistant session',
        tags: ['Assistant'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Session info',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'data', properties: [
                        new OA\Property(property: 'conversation_id', type: 'string', nullable: true, example: 'user-42'),
                    ], type: 'object'),
                ]),
            ),
        ],
    )]
    public function session(): JsonResponse
    {
        return $this->handleCommand(function () {
            $user = $this->securityProvider->getCurrentUser();

            if ($user === null) {
                return HttpResponder::success(['conversation_id' => null]);
            }

            return HttpResponder::success([
                'conversation_id' => 'user-' . $user->getId(),
            ]);
        });
    }
}
