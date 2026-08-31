<?php

declare(strict_types=1);

namespace App\Presentation\Segment\User\Controller\Command;

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
    Segment\User\Request\DestroyProfileRequest,
    Segment\User\Request\ProfileRequest
};

use App\Shared\Enum\AddressType;

class ProfileCommandController extends AbstractCrudCommandController
{
    /**
     * @param UpdateProfileHandlerContract $updateProfileHandler
     * @param DestroyProfileHandlerContract $destroyProfileHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function update(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            ProfileRequest::class,
            fn (ProfileRequest $request) => $this->handleUpdate($request),
        );
    }

    /**
     * @param ProfileRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleUpdate(ProfileRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->updateProfileHandler->handle($payload);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function destroy(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            DestroyProfileRequest::class,
            fn () => $this->destroyProfileHandler->handle(),
        );
    }

    /**
     * @param ProfileRequest $request
     *
     * @return ProfilePayload
    */
    private function createPayload(ProfileRequest $request): ProfilePayload
    {
        return new ProfilePayload(
            firstName: $request->first_name,
            lastName: $request->last_name,
            useShipping: $request->use_shipping,
            billing: $this->createAddress($request, AddressType::BILLING),
            shipping: $this->createAddress($request, AddressType::SHIPPING),
        );
    }

    /**
     * @param ProfileRequest $request
     * @param AddressType $type
     *
     * @return array<string, int|string|null>
    */
    private function createAddress(ProfileRequest $request, AddressType $type): array
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
