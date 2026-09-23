<?php

declare(strict_types=1);

namespace App\Presentation\Assistant;

use Symfony\Component\HttpFoundation\JsonResponse;

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
    /**
     * @param SecurityProviderContract $securityProvider
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly SecurityProviderContract $securityProvider,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @return JsonResponse
    */
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
