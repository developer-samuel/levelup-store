<?php

declare(strict_types=1);

namespace App\Presentation\Segment\User\Controller\Command;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Segment\User\Payload\ChangePasswordPayload;

use App\Core\Ports\{
    Segment\User\Handler\Command\ChangePasswordCommandHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Segment\User\Request\ChangePasswordRequest
};

final class ChangePasswordCommandController extends AbstractCrudCommandController
{
    /**
     * @param ChangePasswordCommandHandlerContract $changePasswordCommandHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        private readonly ChangePasswordCommandHandlerContract $changePasswordCommandHandler,
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
     * @return Response
    */
    public function update(Request $request): Response {
        return $this->executeCommand(
            $request,
            ChangePasswordRequest::class,
            fn(ChangePasswordRequest $changePasswordRequest): array => $this->handleUpdate($changePasswordRequest),
        );
    }

    /**
     * @param ChangePasswordRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleUpdate(ChangePasswordRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->changePasswordCommandHandler->handle($payload);
    }

    /**
     * @param ChangePasswordRequest $request
     *
     * @return ChangePasswordPayload
    */
    private function createPayload(ChangePasswordRequest $request): ChangePasswordPayload
    {
        return new ChangePasswordPayload(
            oldPassword: $request->old_password,
            newPassword: $request->new_password,
            newPasswordConfirmation: $request->new_password_confirmation,
        );
    }
}
