<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Review\Message\ReviewIndexMessage;

use App\Core\Ports\Segment\Review\Repository\ReviewRepositoryContract;

#[AsMessageHandler]
final readonly class ReviewIndexMessageHandler
{
    public function __construct(
        private ReviewRepositoryContract $reviewRepository,
        private ReviewProjector $projector,
    ) {}

    public function __invoke(ReviewIndexMessage $message): void
    {
        $review = $this->reviewRepository->findById($message->reviewId);

        if ($review === null) {
            return;
        }

        $this->projector->index($review);
    }
}
