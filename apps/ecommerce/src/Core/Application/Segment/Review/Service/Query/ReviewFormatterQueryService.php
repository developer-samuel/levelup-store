<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Query;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Review\Repository\ReviewRepositoryContract,
    Segment\Review\Service\Query\ReviewFormatterQueryContract,
    Segment\Review\Service\Query\ReviewRatingQueryContract
};

/**
 * @phpstan-import-type ReviewWithRatings from ReviewFormatterQueryContract
 * @phpstan-import-type ReviewData from ReviewFormatterQueryContract
*/
final readonly class ReviewFormatterQueryService implements ReviewFormatterQueryContract
{
    /**
     * @param ReviewRepositoryContract $reviewRepository
     * @param ReviewRatingQueryContract $reviewRatingQuery
    */
    public function __construct(
        private ReviewRepositoryContract $reviewRepository,
        private ReviewRatingQueryContract $reviewRatingQuery,
    ) {}

    /**
     * @param int $variantId
     * @param User|null $user
     *
     * @return array<int, ReviewWithRatings>
    */
    public function getFormattedReviewsForVariant(int $variantId, ?User $user): array
    {
        $authUserId = $user?->getId();

        $reviews = $this->reviewRepository->findAllByVariant($variantId, $authUserId);

        return $this->formatReviewsWithRatings($reviews, $user);
    }

    /**
     * @param int $variantId
     *
     * @return ReviewData
    */
    public function getFormattedReviewData(int $variantId): array
    {
        $rawData = $this->reviewRepository->getReviewsAndAverageByVariant($variantId);

        $reviews = $this->reviewRepository->findAllByVariant($variantId);

        return $this->formatReviewData($rawData, $reviews);
    }

    /**
     * @param Review[] $reviews
     * @param User|null $user
     *
     * @return array<int, ReviewWithRatings>
    */
    private function formatReviewsWithRatings(array $reviews, ?User $user): array
    {
        $result = [];
        foreach ($reviews as $review) {
            $result[] = $this->formatSingleReviewWithRatings($review, $user);
        }

        return $result;
    }

    /**
     * @param ReviewData $rawData
     * @param Review[] $reviews
     *
     * @return ReviewData
    */
    private function formatReviewData(array $rawData, array $reviews): array
    {
        return [
            'average'        => $rawData['average'],
            'totalRatings'   => $rawData['totalRatings'] ?? count($reviews),
            'totalFeedbacks' => $rawData['totalFeedbacks'] ?? 0,
            'ratingsCount'   => $rawData['ratingsCount'] ?? [],
        ];
    }

    /**
     * @param Review $review
     * @param User|null $user
     *
     * @return ReviewWithRatings
    */
    private function formatSingleReviewWithRatings(Review $review, ?User $user): array
    {
        $stats = $this->reviewRatingQuery->getReviewFeedbackStats($review, $user);

        return [
            'review'         => $review,
            'likesCount'     => (int) ($stats['likesCount'] ?? 0),
            'dislikesCount'  => (int) ($stats['dislikesCount'] ?? 0),
            'userRatingType' => (string) ($stats['userRatingType'] ?? ''),
        ];
    }
}
