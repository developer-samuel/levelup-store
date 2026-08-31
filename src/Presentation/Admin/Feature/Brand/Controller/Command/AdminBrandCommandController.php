<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Brand\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use Kit\{
    Assertion\Shared\IdAssertion,
    Utils\Shared\Sanitizer\DataSanitizer
};

use App\Core\Domain\Admin\Segment\Brand\Payload\AdminBrandPayload;

use App\Core\Application\Admin\Segment\Brand\Handler\Command\AdminBrandCommandHandler;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Admin\Feature\Brand\Request\AdminBrandStoreRequest,
    Admin\Feature\Brand\Request\AdminBrandUpdateRequest
};

class AdminBrandCommandController extends AbstractCrudCommandController
{
    /**
     * @param HmacFieldDecoderContract $hmacFieldDecoder
     * @param AdminBrandCommandHandler $adminBrandHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        private readonly HmacFieldDecoderContract $hmacFieldDecoder,
        private readonly AdminBrandCommandHandler $adminBrandHandler,
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
            AdminBrandStoreRequest::class,
            fn(AdminBrandStoreRequest $req): array => $this->handleCreate($req),
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
            AdminBrandUpdateRequest::class,
            fn(AdminBrandUpdateRequest $req): array => $this->handleUpdate($req),
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function destroy(Request $request): JsonResponse
    {
        return $this->executeDeleteCommand(
            $request,
            fn(int $id): array => $this->adminBrandHandler->handleDestroy($id),
        );
    }

    /**
     * @param AdminBrandStoreRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleCreate(AdminBrandStoreRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->adminBrandHandler->handleCreate($payload);
    }

    /**
     * @param AdminBrandUpdateRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleUpdate(AdminBrandUpdateRequest $request): array
    {
        $id = $this->decodeId($request);

        $payload = $this->createPayload($request, $id);

        return $this->adminBrandHandler->handleUpdate($payload);
    }

    /**
     * @param AdminBrandUpdateRequest $request
     *
     * @return int
    */
    private function decodeId(AdminBrandUpdateRequest $request): int
    {
        $decoded = DataSanitizer::sanitizeInt(
            $this->hmacFieldDecoder->decode($request, 'id'),
        );

        return IdAssertion::assert(
            $decoded,
            'Brand ID',
        );
    }

    /**
     * @param AdminBrandStoreRequest|AdminBrandUpdateRequest $request
     * @param int|null $id
     *
     * @return AdminBrandPayload
    */
    private function createPayload(AdminBrandStoreRequest|AdminBrandUpdateRequest $request, ?int $id = null): AdminBrandPayload
    {
        return new AdminBrandPayload(
            name: $request->name,
            id: $id !== null ? (string) $id : null,
        );
    }
}
