<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Order\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Admin\Segment\Order\Payload\AdminOrderStatusPayload;

use App\Core\Application\Admin\Segment\Order\Handler\Command\AdminOrderCommandHandler;

use App\Core\Ports\Shared\Logging\AppLoggerContract;

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Admin\Feature\Order\Request\AdminOrderStatusRequest
};

class AdminOrderStatusCommandController extends AbstractCrudCommandController
{
    /**
     * @param AdminOrderCommandHandler $updateOrderHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
    public function __construct(
        private readonly AdminOrderCommandHandler $updateOrderHandler,
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
    public function update(Request $request): JsonResponse {
        return $this->executeCommand(
            $request,
            AdminOrderStatusRequest::class,
            fn(AdminOrderStatusRequest $orderRequest): array => $this->handleUpdate($orderRequest),
        );
    }

    /**
     * @param AdminOrderStatusRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleUpdate(AdminOrderStatusRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->updateOrderHandler->handle($payload);
    }

    /**
     * @param AdminOrderStatusRequest $request
    */
    private function createPayload(AdminOrderStatusRequest $request): AdminOrderStatusPayload
    {
        return new AdminOrderStatusPayload(
            code: $request->code,
            status: $request->status,
        );
    }
}
