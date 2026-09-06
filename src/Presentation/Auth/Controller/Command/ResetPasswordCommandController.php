<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Controller\Command;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\JsonResponse,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Auth\Payload\ResetPasswordPayload;

use App\Core\Ports\{
    Auth\Handler\Command\ResetPasswordCommandHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Auth\Request\ResetPasswordRequest
};

final class ResetPasswordCommandController extends AbstractCrudCommandController
{
    /**
     * @param ResetPasswordCommandHandlerContract $resetPasswordCommandHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        private readonly ResetPasswordCommandHandlerContract $resetPasswordCommandHandler,
        CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
        ValidatorInterface $validator,
    ) {
        parent::__construct(
            $csrfTokenManager,
            $logger,
            $validator,
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            ResetPasswordRequest::class,
            fn(ResetPasswordRequest $resetPasswordRequest): array => $this->handleStore($resetPasswordRequest),
        );
    }

    /**
     * @param ResetPasswordRequest $request
     *
     * @return array<string, mixed>
     */
    private function handleStore(ResetPasswordRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->resetPasswordCommandHandler->handle($payload);
    }

    /**
     * @param ResetPasswordRequest $request
     *
     * @return ResetPasswordPayload
    */
    private function createPayload(ResetPasswordRequest $request): ResetPasswordPayload
    {
        return new ResetPasswordPayload(
            token: $request->getToken(),
            password: $request->password,
            passwordConfirmation: $request->password_confirmation,
        );
    }
}
