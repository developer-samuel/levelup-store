<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\Review\Message\ReviewIndexMessage;

use App\Core\Ports\Segment\Review\Repository\ReviewRepositoryContract;

#[AsMessageHandler]
final readonly class ReviewIndexMessageHandler
{
    /**
     * @param ReviewRepositoryContract $reviewRepository
     * @param ReviewProjector $projector
    */
    public function __construct(
        private ReviewRepositoryContract $reviewRepository,
        private ReviewProjector $projector,
    ) {}

    /**
     * @param ReviewIndexMessage $message
     *
     * @return void
    */
    public function __invoke(ReviewIndexMessage $message): void
    {
        $review = $this->reviewRepository->findById($message->reviewId);

        if ($review === null) {
            return;
        }

        $this->projector->index($review);
    }
}
