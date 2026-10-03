<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Order\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\Admin\Order\AdminOrderStatusPayload;

use App\Core\Application\Admin\Segment\Order\Handler\AdminOrderCommandHandler;

use App\Core\Ports\Shared\Logging\AppLoggerContract;

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Web\Admin\Order\Request\AdminOrderStatusRequest
};

final class AdminOrderStatusCommandController extends AbstractCrudCommandController
{
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

    public function update(Request $request): JsonResponse {
        return $this->executeCommand(
            $request,
            AdminOrderStatusRequest::class,
            fn(AdminOrderStatusRequest $orderRequest): array => $this->handleUpdate($orderRequest),
        );
    }

    /** @return array<string, mixed> */
    private function handleUpdate(AdminOrderStatusRequest $request): array
    {
        $payload = $this->createPayload($request);

        return $this->updateOrderHandler->handle($payload);
    }

    private function createPayload(AdminOrderStatusRequest $request): AdminOrderStatusPayload
    {
        return new AdminOrderStatusPayload(
            code: $request->code,
            status: $request->status,
        );
    }
}
