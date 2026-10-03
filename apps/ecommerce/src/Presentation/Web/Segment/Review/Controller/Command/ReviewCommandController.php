<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Review\Controller\Command;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\JsonResponse,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Core\Domain\{
    Segment\Review\Payload\ReviewCreatePayload,
    Segment\Review\Payload\ReviewDestroyPayload,
};

use App\Core\Ports\{
    Segment\Review\Handler\Command\DestroyReviewHandlerContract,
    Segment\Review\Handler\Command\ReviewCommandHandlerContract,
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Shared\Utils\IdDecoder,
    Web\Segment\Review\Request\ReviewDestroyRequest,
    Web\Segment\Review\Request\ReviewStoreRequest
};

final class ReviewCommandController extends AbstractCrudCommandController
{
    public function __construct(
        private readonly HmacFieldDecoderContract $hmacFieldDecoder,
        private readonly ReviewCommandHandlerContract $reviewCommandHandler,
        private readonly DestroyReviewHandlerContract $destroyReviewHandler,
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
            ReviewStoreRequest::class,
            fn(ReviewStoreRequest $request): array => $this->handleStore($request),
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            ReviewDestroyRequest::class,
            fn(ReviewDestroyRequest $request): array => $this->handleDestroy($request),
        );
    }

    /** @return array<string, mixed> */
    private function handleStore(ReviewStoreRequest $request): array
    {
        $variantId = $this->decodeVariantId($request);

        $payload = $this->createPayload($request, $variantId);

        return $this->reviewCommandHandler->handle($payload);
    }

    /** @return array<string, mixed> */
    private function handleDestroy(ReviewDestroyRequest $request): array
    {
        $reviewId = $this->decodeReviewId($request);

        $payload = $this->createDestroyPayload($reviewId);

        return $this->destroyReviewHandler->handle($payload);
    }

    private function decodeVariantId(ReviewStoreRequest $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'variantId',
        );
    }

    private function decodeReviewId(ReviewDestroyRequest $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'reviewId',
        );
    }

    private function createPayload(ReviewStoreRequest $request, int $decodedVariantId): ReviewCreatePayload
    {
        return new ReviewCreatePayload(
            variantId: $decodedVariantId,
            value: (int) $request->value,
            positives: $request->positives ?? [],
            negatives: $request->negatives ?? [],
            body: $request->body,
        );
    }
    
    private function createDestroyPayload(int $decodedReviewId): ReviewDestroyPayload
    {
        return new ReviewDestroyPayload(
            reviewId: $decodedReviewId,
        );
    }
}
