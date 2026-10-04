<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Product\Controller\Command\Variant;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Admin\Product\Payload\AdminVariantDescriptionPayload;

use App\Core\Application\Admin\Segment\Product\Handler\Command\AdminVariantDescriptionCommandHandler;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Web\Admin\Product\Controller\Command\Variant\Abstract\AbstractAdminVariantCommandController,
    Web\Admin\Product\Request\Variant\Description\AdminVariantDescriptionStoreRequest,
    Web\Admin\Product\Request\Variant\Description\AdminVariantDescriptionUpdateRequest
};

final class AdminVariantDescriptionCommandController extends AbstractAdminVariantCommandController
{
    public function __construct(
        private readonly AdminVariantDescriptionCommandHandler $adminVariantDescriptionHandler,
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
        return sprintf('Description %s successfully.', $action);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            AdminVariantDescriptionStoreRequest::class,
            fn(AdminVariantDescriptionStoreRequest $req): array => $this->handleCreateCommand(
                $req,
                fn(AdminVariantDescriptionStoreRequest $r): AdminVariantDescriptionPayload => $this->createPayload($r),
                fn(AdminVariantDescriptionPayload $payload): array                         => $this->adminVariantDescriptionHandler->handleCreate($payload),
            ),
        );
    }

    public function update(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            AdminVariantDescriptionUpdateRequest::class,
            fn(AdminVariantDescriptionUpdateRequest $req): array => $this->handleUpdateCommand(
                $req,
                fn(AdminVariantDescriptionUpdateRequest $r, string $id): AdminVariantDescriptionPayload => $this->createPayload($r, $id),
                fn(AdminVariantDescriptionPayload $payload): array                                      => $this->adminVariantDescriptionHandler->handleUpdate($payload),
            ),
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->executeDeleteCommand(
            $request,
            fn(int $id): array => $this->adminVariantDescriptionHandler->handleDestroy($id),
        );
    }

    private function createPayload(
        AdminVariantDescriptionStoreRequest|AdminVariantDescriptionUpdateRequest $req,
        ?string $id = null,
    ): AdminVariantDescriptionPayload {
        return new AdminVariantDescriptionPayload(
            position: $req->position,
            title: $req->title,
            body: $req->body,
            variantId: $req->variantId,
            id: $id,
        );
    }
}
