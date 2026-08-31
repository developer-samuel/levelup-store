<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Controller\Command\Variant;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Admin\Segment\Product\Payload\Variant\AdminVariantEanPayload;

use App\Core\Application\Admin\Segment\Product\Handler\Command\Variant\AdminVariantEanCommandHandler;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Feature\Product\Controller\Command\Variant\Abstract\AbstractAdminVariantCommandController,
    Admin\Feature\Product\Request\Variant\Ean\AdminVariantEanStoreRequest,
    Admin\Feature\Product\Request\Variant\Ean\AdminVariantEanUpdateRequest
};

class AdminVariantEanCommandController extends AbstractAdminVariantCommandController
{
    /**
     * @param AdminVariantEanCommandHandler $adminVariantEanHandler
     * @param HmacFieldDecoderContract $hmacFieldDecoder
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
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

    /**
     * @param string $action
     *
     * @return string
    */
    protected function getSuccessMessage(string $action): string
    {
        return sprintf('EAN %s successfully.', $action);
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
            AdminVariantEanStoreRequest::class,
            fn(AdminVariantEanStoreRequest $req): array => $this->handleCreateCommand(
                $req,
                fn(AdminVariantEanStoreRequest $r): AdminVariantEanPayload => $this->createPayload($r),
                fn(AdminVariantEanPayload $payload): array                 => $this->adminVariantEanHandler->handleCreate($payload),
            ),
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
            AdminVariantEanUpdateRequest::class,
            fn(AdminVariantEanUpdateRequest $req): array => $this->handleUpdateCommand(
            $req,
            fn(AdminVariantEanUpdateRequest $r, string $id): AdminVariantEanPayload => $this->createPayload($r, $id),
            fn(AdminVariantEanPayload $payload): array                              => $this->adminVariantEanHandler->handleUpdate($payload),
        ),
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
            fn(int $id): array => $this->adminVariantEanHandler->handleDestroy($id),
        );
    }

    /**
     * @param AdminVariantEanStoreRequest|AdminVariantEanUpdateRequest $req
     * @param string|null $id
     *
     * @return AdminVariantEanPayload
    */
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
