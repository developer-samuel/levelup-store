<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Cart\Controller\Command;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use Kit\Utils\Shared\Sanitizer\DataSanitizer;

use App\Core\Ports\{
    Segment\Cart\Service\Command\CartMutationCommandContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCommandController,
    Shared\Responder\HttpResponder,
    Segment\Cart\Request\CartDestroyRequest,
    Segment\Cart\Request\CartStoreRequest
};

class CartCommandController extends AbstractCommandController
{
    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param CartMutationCommandContract $cartMutationCommand
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly CartMutationCommandContract $cartMutationCommand,
        AppLoggerContract $logger,
    ) {
        parent::__construct($logger);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function store(Request $request): JsonResponse
    {
        return $this->executeCartCommand(
            $request,
            CartStoreRequest::class,
            true,
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function destroy(Request $request): JsonResponse
    {
        return $this->executeCartCommand(
            $request,
            CartDestroyRequest::class,
            false,
        );
    }

    /**
     * @param Request $request
     * @param string $requestClass
     * @param bool $add
     *
     * @return JsonResponse
    */
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

    /**
     * @param CartStoreRequest|CartDestroyRequest $cartRequest
     *
     * @return int
    */
    private function getCartRequestId(CartStoreRequest|CartDestroyRequest $cartRequest): int
    {
        if ($cartRequest instanceof CartStoreRequest) {
            return $cartRequest->variantId;
        }

        return $cartRequest->itemId;
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return JsonResponse
    */
    private function createCartResponse(array $result): JsonResponse
    {
        $message = DataSanitizer::sanitizeString($result['message'] ?? '');
        $success = $result['success'] ?? true;

        return $success
            ? HttpResponder::success($result, $message)
            : HttpResponder::unprocessableEntity([], $message);
    }
}
