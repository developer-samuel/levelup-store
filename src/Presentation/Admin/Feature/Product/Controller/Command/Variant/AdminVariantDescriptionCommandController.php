<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Controller\Command\Variant;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Admin\Segment\Product\Payload\Variant\AdminVariantDescriptionPayload;

use App\Core\Application\Admin\Segment\Product\Handler\Command\Variant\AdminVariantDescriptionCommandHandler;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Admin\Feature\Product\Controller\Command\Variant\Abstract\AbstractAdminVariantCommandController,
    Admin\Feature\Product\Request\Variant\Description\AdminVariantDescriptionStoreRequest,
    Admin\Feature\Product\Request\Variant\Description\AdminVariantDescriptionUpdateRequest
};

class AdminVariantDescriptionCommandController extends AbstractAdminVariantCommandController
{
    /**
     * @param AdminVariantDescriptionCommandHandler $adminVariantDescriptionHandler
     * @param HmacFieldDecoderContract $hmacFieldDecoder
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
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

    /**
     * @param string $action
     *
     * @return string
    */
    protected function getSuccessMessage(string $action): string
    {
        return sprintf('Description %s successfully.', $action);
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
            AdminVariantDescriptionStoreRequest::class,
            fn(AdminVariantDescriptionStoreRequest $req): array => $this->handleCreateCommand(
                $req,
                fn(AdminVariantDescriptionStoreRequest $r): AdminVariantDescriptionPayload => $this->createPayload($r),
                fn(AdminVariantDescriptionPayload $payload): array                         => $this->adminVariantDescriptionHandler->handleCreate($payload),
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
            AdminVariantDescriptionUpdateRequest::class,
            fn(AdminVariantDescriptionUpdateRequest $req): array => $this->handleUpdateCommand(
                $req,
                fn(AdminVariantDescriptionUpdateRequest $r, string $id): AdminVariantDescriptionPayload => $this->createPayload($r, $id),
                fn(AdminVariantDescriptionPayload $payload): array                                      => $this->adminVariantDescriptionHandler->handleUpdate($payload),
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
            fn(int $id): array => $this->adminVariantDescriptionHandler->handleDestroy($id),
        );
    }

    /**
     * @param AdminVariantDescriptionStoreRequest|AdminVariantDescriptionUpdateRequest $req
     * @param string|null $id
     *
     * @return AdminVariantDescriptionPayload
    */
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
