<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Brand\Controller;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use Packages\Kit\{
    Assertion\Shared\IdAssertion,
    Utils\Shared\DataSanitizer
};

use App\Core\Domain\Admin\Brand\AdminBrandPayload;

use App\Core\Application\Admin\Segment\Brand\Handler\AdminBrandCommandHandler;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Web\Admin\Brand\Request\AdminBrandStoreRequest,
    Web\Admin\Brand\Request\AdminBrandUpdateRequest
};

final class AdminBrandCommandController extends AbstractCrudCommandController
{
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

    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            AdminBrandStoreRequest::class,
            fn(AdminBrandStoreRequest $req): array => $this->handleCreate($req),
        );
    }

    public function update(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            AdminBrandUpdateRequest::class,
            fn(AdminBrandUpdateRequest $req): array => $this->handleUpdate($req),
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->executeDeleteCommand(
            $request,
            fn(int $id): array => $this->adminBrandHandler->handleDestroy($id),
        );
    }

    /** @return array<string, mixed> */
    private function handleCreate(AdminBrandStoreRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->adminBrandHandler->handleCreate($payload);
    }

    /** @return array<string, mixed> */
    private function handleUpdate(AdminBrandUpdateRequest $request): array
    {
        $id = $this->decodeId($request);

        $payload = $this->createPayload($request, $id);

        return $this->adminBrandHandler->handleUpdate($payload);
    }

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

    private function createPayload(AdminBrandStoreRequest|AdminBrandUpdateRequest $request, ?int $id = null): AdminBrandPayload
    {
        return new AdminBrandPayload(
            name: $request->name,
            id: $id !== null ? (string) $id : null,
        );
    }
}
