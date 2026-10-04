<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Controller\Command;

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
    Web\Auth\Request\ResetPasswordRequest
};

final class ResetPasswordCommandController extends AbstractCrudCommandController
{
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

    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            ResetPasswordRequest::class,
            fn(ResetPasswordRequest $resetPasswordRequest): array => $this->handleStore($resetPasswordRequest),
        );
    }

    /** @return array<string, mixed> */
    private function handleStore(ResetPasswordRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->resetPasswordCommandHandler->handle($payload);
    }

    private function createPayload(ResetPasswordRequest $request): ResetPasswordPayload
    {
        return new ResetPasswordPayload(
            token: $request->getToken(),
            password: $request->password,
            passwordConfirmation: $request->password_confirmation,
        );
    }
}
