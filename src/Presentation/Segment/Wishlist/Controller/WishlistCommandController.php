<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Wishlist\Controller;

use Symfony\{
    Component\HttpFoundation\JsonResponse,
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use App\Core\Domain\Segment\Wishlist\Payload\WishlistPayload;

use App\Core\Ports\{
    Segment\Wishlist\Handler\Command\DestroyWishlistHandlerContract,
    Segment\Wishlist\Handler\Command\ToggleWishlistHandlerContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Segment\Wishlist\Request\WishlistDestroyRequest,
    Segment\Wishlist\Request\WishlistToggleRequest
};

class WishlistCommandController extends AbstractCrudCommandController
{
    /**
     * @param ToggleWishlistHandlerContract $toggleWishlistHandler
     * @param DestroyWishlistHandlerContract $destroyWishlistHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ToggleWishlistHandlerContract $toggleWishlistHandler,
        private readonly DestroyWishlistHandlerContract $destroyWishlistHandler,
        CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $csrfTokenManager,
            $logger,
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function toggle(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            WishlistToggleRequest::class,
            fn (WishlistToggleRequest $request) => $this->handleToggle($request),
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function destroy(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            WishlistDestroyRequest::class,
            fn (WishlistDestroyRequest $request) => $this->handleDestroy($request),
        );
    }

    /**
     * @param WishlistToggleRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleToggle(WishlistToggleRequest $request): array
    {
        $payload = new WishlistPayload($request->variantId);
        $exists = $this->toggleWishlistHandler->handle($payload);

        return ['exists' => $exists];
    }

    /**
     * @param WishlistDestroyRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleDestroy(WishlistDestroyRequest $request): array
    {
        $payload = new WishlistPayload($request->variantId);
        $exists = $this->destroyWishlistHandler->handle($payload);

        return ['exists' => $exists];
    }
}
