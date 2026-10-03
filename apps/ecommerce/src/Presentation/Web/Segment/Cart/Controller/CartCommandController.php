<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Cart\Controller;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use Packages\Kit\Utils\Shared\DataSanitizer;

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartMutationCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCommandController,
    Shared\Responder\HttpResponder,
    Web\Segment\Cart\Request\CartDestroyRequest,
    Web\Segment\Cart\Request\CartStoreRequest
};

final class CartCommandController extends AbstractCommandController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly CartMutationCommandContract $cartMutationCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->executeCartCommand(
            $request,
            CartStoreRequest::class,
            true,
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->executeCartCommand(
            $request,
            CartDestroyRequest::class,
            false,
        );
    }

    private function executeCartCommand(Request $request, string $requestClass, bool $add): JsonResponse
    {
        return $this->handleCommand(function () use ($request, $requestClass, $add) {
            /** @var CartStoreRequest|CartDestroyRequest $cartRequest */
            $cartRequest = $requestClass::fromHttpRequest($request, $this->csrfTokenManager);

            $id = $this->getCartRequestId($cartRequest);
            if ($id <= 0) {
                return HttpResponder::unprocessableEntity([], 'Please select a valid item.');
            }

            $result = $add
                ? $this->cartMutationCommand->addToCart($id)
                : $this->cartMutationCommand->removeFromCart($id);

            return $this->createCartResponse($result);
        });
    }

    private function getCartRequestId(CartStoreRequest|CartDestroyRequest $cartRequest): int
    {
        if ($cartRequest instanceof CartStoreRequest) {
            return $cartRequest->variantId;
        }

        return $cartRequest->itemId;
    }

    /** @param array<string, mixed> $result */
    private function createCartResponse(array $result): JsonResponse
    {
        $message = DataSanitizer::sanitizeString($result['message'] ?? '');
        $success = $result['success'] ?? true;

        return (bool) $success
            ? HttpResponder::success($result, $message)
            : HttpResponder::unprocessableEntity([], $message);
    }
}
