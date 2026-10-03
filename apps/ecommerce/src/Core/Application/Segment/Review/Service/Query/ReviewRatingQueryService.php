<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Query;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Review\Repository\ReviewRatingRepositoryContract,
    Segment\Review\Service\Query\ReviewRatingQueryContract
};

final readonly class ReviewRatingQueryService implements ReviewRatingQueryContract
{
    public function __construct(
        private ReviewRatingRepositoryContract $reviewRatingRepository,
    ) {}

    /** @return array<string, int|string> */
    public function getReviewFeedbackStats(Review $review, ?User $user): array
    {
        $likes = $this->getRatingCount($review, 'like');
        $dislikes = $this->getRatingCount($review, 'dislike');
        $userRatingType = $this->getUserRatingType($review, $user);

        return [
            'likesCount'     => $likes,
            'dislikesCount'  => $dislikes,
            'userRatingType' => $userRatingType,
        ];
    }

    private function getRatingCount(Review $review, string $type): int
    {
        $reviewId = $review->getId();

        return $this->reviewRatingRepository->countByType($reviewId, $type);
    }

    private function getUserRatingType(Review $review, ?User $user): string
    {
        if ($user === null) {
            return '';
        }

        $rating = $this->reviewRatingRepository->findRatingByUser($review, $user);

        return $rating?->getType()->value ?? '';
    }
}
