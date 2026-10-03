<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Product\Controller\Command\Variant;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Admin\Product\Payload\AdminVariantEanPayload;

use App\Core\Application\Admin\Segment\Product\Handler\Command\AdminVariantEanCommandHandler;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Web\Admin\Product\Controller\Command\Variant\Abstract\AbstractAdminVariantCommandController,
    Web\Admin\Product\Request\Variant\Ean\AdminVariantEanStoreRequest,
    Web\Admin\Product\Request\Variant\Ean\AdminVariantEanUpdateRequest
};

final class AdminVariantEanCommandController extends AbstractAdminVariantCommandController
{
    public function __construct(
        private readonly AdminVariantEanCommandHandler $adminVariantEanHandler,
        HmacFieldDecoderContract $hmacFieldDecoder,
        CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
        ValidatorInterface $validator,
    ) {
        parent::__construct(
            $hmacFieldDecoder,
            $csrfTokenManager,
            $logger,
            $validator,
        );
    }

    protected function getSuccessMessage(string $action): string
    {
        return sprintf('EAN %s successfully.', $action);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            AdminVariantEanStoreRequest::class,
            fn(AdminVariantEanStoreRequest $req): array => $this->handleCreateCommand(
                $req,
                fn(AdminVariantEanStoreRequest $r): AdminVariantEanPayload => $this->createPayload($r),
                fn(AdminVariantEanPayload $payload): array                 => $this->adminVariantEanHandler->handleCreate($payload),
            ),
        );
    }

    public function update(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            AdminVariantEanUpdateRequest::class,
            fn(AdminVariantEanUpdateRequest $req): array => $this->handleUpdateCommand(
            $req,
            fn(AdminVariantEanUpdateRequest $r, string $id): AdminVariantEanPayload => $this->createPayload($r, $id),
            fn(AdminVariantEanPayload $payload): array                              => $this->adminVariantEanHandler->handleUpdate($payload),
        ),
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->executeDeleteCommand(
            $request,
            fn(int $id): array => $this->adminVariantEanHandler->handleDestroy($id),
        );
    }

    private function createPayload(
        AdminVariantEanStoreRequest|AdminVariantEanUpdateRequest $req,
        ?string $id = null,
    ): AdminVariantEanPayload {
        return new AdminVariantEanPayload(
            code: $req->code,
            variantId: $req->variantId,
            id: $id,
        );
    }
}
