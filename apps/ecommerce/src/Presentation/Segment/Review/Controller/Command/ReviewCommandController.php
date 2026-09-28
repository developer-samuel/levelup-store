<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Review\Controller\Command;

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
    Segment\Review\Request\ReviewDestroyRequest,
    Segment\Review\Request\ReviewStoreRequest,
    Shared\Utils\IdDecoder
};

final class ReviewCommandController extends AbstractCrudCommandController
{
    /**
     * @param HmacFieldDecoderContract $hmacFieldDecoder
     * @param ReviewCommandHandlerContract $reviewCommandHandler
     * @param DestroyReviewHandlerContract $destroyReviewHandler
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param AppLoggerContract $logger
     * @param ValidatorInterface $validator
    */
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function store(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            ReviewStoreRequest::class,
            fn(ReviewStoreRequest $request): array => $this->handleStore($request),
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
            ReviewDestroyRequest::class,
            fn(ReviewDestroyRequest $request): array => $this->handleDestroy($request),
        );
    }

    /**
     * @param ReviewStoreRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleStore(ReviewStoreRequest $request): array
    {
        $variantId = $this->decodeVariantId($request);

        $payload = $this->createPayload($request, $variantId);

        return $this->reviewCommandHandler->handle($payload);
    }

    /**
     * @param ReviewDestroyRequest $request
     *
     * @return array<string, mixed>
    */
    private function handleDestroy(ReviewDestroyRequest $request): array
    {
        $reviewId = $this->decodeReviewId($request);

        $payload = $this->createDestroyPayload($reviewId);

        return $this->destroyReviewHandler->handle($payload);
    }

    /**
     * @param ReviewStoreRequest $request
     *
     * @return int
    */
    private function decodeVariantId(ReviewStoreRequest $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'variantId',
        );
    }

    /**
     * @param ReviewDestroyRequest $request
     *
     * @return int
    */
    private function decodeReviewId(ReviewDestroyRequest $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'reviewId',
        );
    }

    /**
     * @param ReviewStoreRequest $request
     * @param int $decodedVariantId
     *
     * @return ReviewCreatePayload
    */
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

    /**
     * @param int $decodedReviewId
     *
     * @return ReviewDestroyPayload
    */
    private function createDestroyPayload(int $decodedReviewId): ReviewDestroyPayload
    {
        return new ReviewDestroyPayload(
            reviewId: $decodedReviewId,
        );
    }
}
