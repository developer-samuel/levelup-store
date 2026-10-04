<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Review\Controller\Command;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\JsonResponse,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use App\Core\Domain\Segment\Review\Payload\ReviewRatingPayload;

use App\Core\Ports\{
    Segment\Review\Handler\Command\ToggleReviewRatingHandlerContract,
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Command\AbstractCrudCommandController,
    Shared\Utils\IdDecoder,
    Web\Segment\Review\Request\ReviewRatingRequest
};

final class ReviewRatingCommandController extends AbstractCrudCommandController
{
    public function __construct(
        private readonly HmacFieldDecoderContract $hmacFieldDecoder,
        private readonly ToggleReviewRatingHandlerContract $toggleReviewRatingHandler,
        CsrfTokenManagerInterface $csrfTokenManager,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $csrfTokenManager,
            $logger,
        );
    }

    public function toggle(Request $request): JsonResponse
    {
        return $this->executeCommand(
            $request,
            ReviewRatingRequest::class,
            fn(ReviewRatingRequest $request): array => $this->handleToggle($request),
        );
    }

    /** @return array<string, mixed> */
    private function handleToggle(ReviewRatingRequest $request): array
    {
        $decodedReviewId = $this->decodeReviewId($request);

        $payload = new ReviewRatingPayload(
            reviewId: $decodedReviewId,
            type: $request->type,
        );

        return $this->toggleReviewRatingHandler->handle($payload);
    }

    private function decodeReviewId(ReviewRatingRequest $request): int
    {
        return IdDecoder::decode(
            $this->hmacFieldDecoder,
            $request,
            'reviewId',
        );
    }
}
