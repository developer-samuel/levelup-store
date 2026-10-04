<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\User\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Segment\User\Payload\ProfilePayload;

use App\Core\Ports\{
    Segment\User\Handler\Command\DestroyProfileHandlerContract,
    Segment\User\Handler\Command\UpdateProfileHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Web\Segment\User\Request\DestroyProfileRequest,
    Web\Segment\User\Request\UpdateProfileRequest
};

use App\Shared\Enum\AddressType;

final class ProfileCommandController extends AbstractCrudCommandController
{
    public function __construct(
        private readonly UpdateProfileHandlerContract $updateProfileHandler,
        private readonly DestroyProfileHandlerContract $destroyProfileHandler,
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

    public function update(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            UpdateProfileRequest::class,
            fn (UpdateProfileRequest $request) => $this->handleUpdate($request),
        );
    }

    /** @return array<string, mixed> */
    private function handleUpdate(UpdateProfileRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->updateProfileHandler->handle($payload);
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            DestroyProfileRequest::class,
            fn () => $this->destroyProfileHandler->handle(),
        );
    }

    private function createPayload(UpdateProfileRequest $request): ProfilePayload
    {
        return new ProfilePayload(
            firstName: $request->first_name,
            lastName: $request->last_name,
            useShipping: $request->use_shipping,
            billing: $this->createAddress($request, AddressType::BILLING),
            shipping: $this->createAddress($request, AddressType::SHIPPING),
        );
    }

    /** @return array<string, int|string|null> */
    private function createAddress(UpdateProfileRequest $request, AddressType $type): array
    {
        $prefix = $type->value;

        return [
            'country'    => $request->{$prefix . '_country'},
            'street'     => $request->{$prefix . '_street'},
            'postalCode' => $request->{$prefix . '_postal_code'},
            'city'       => $request->{$prefix . '_city'},
        ];
    }
}
