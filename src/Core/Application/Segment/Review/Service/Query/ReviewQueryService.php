<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Query;

use Kit\Assertion\Shared\IdAssertion;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewDetail,
    Segment\Review\ValueObject\ReviewListObject,
    Segment\Review\ValueObject\ReviewObject,
    Segment\Review\ValueObject\ReviewStatsObject,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Order\Repository\OrderItemRepositoryContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Segment\Review\Service\Query\ReviewQueryContract
};

final readonly class ReviewQueryService implements ReviewQueryContract
{
    private const MAX_DETAILS = 5;

    /**
     * @param OrderItemRepositoryContract $orderItemRepository
     * @param ReviewRepositoryContract $reviewRepository
    */
    public function __construct(
        private OrderItemRepositoryContract $orderItemRepository,
        private ReviewRepositoryContract $reviewRepository,
    ) {}

    /**
     * @param int $variantId
     *
     * @return float
    */
    public function getAverageRatingByVariant(int $variantId): float
    {
        $reviewData = $this->reviewRepository->getReviewsAndAverageByVariant($variantId);

        return (float) $reviewData['average'];
    }

    /**
     * @param list<int> $variantIds
     *
     * @return array<int, float> [variantId => average]
    */
    public function getAverageRatingsForVariants(array $variantIds): array
    {
        if (empty($variantIds)) {
            return [];
        }

        return $this->reviewRepository->getAverageRatingsByVariantIds($variantIds);
    }

    /**
     * @param ProductVariant $variant
     * @param User|null $user
     *
     * @return ReviewListObject
     *
     * @throws \LogicException
    */
    public function getLastReviewData(ProductVariant $variant, ?User $user): ReviewListObject
    {
        $variantId = $this->assertVariantId($variant);
        $reviewExists = $this->canUserReview($user, $variantId);

        $reviewData = $this->reviewRepository->getReviewsAndAverageByVariant($variantId);

        $stats = ReviewStatsObject::fromArray($reviewData);

        $reviews = $this->mapReviewsToObjects($reviewData['reviews'] ?? []);

        $lastReviewData = $this->getLastReviewWithDetails($variantId);

        $lastReview = $lastReviewData['lastReview'];
        $lastReviewDetails = $lastReviewData['details'];

        return new ReviewListObject(
            reviewExists: $reviewExists,
            reviews: $reviews,
            averageRating: (float) ($reviewData['average'] ?? 0.0),
            totalRatings: $stats->totalRatings,
            totalFeedbacks: $stats->totalFeedbacks,
            totalCount: $stats->getTotalCount(),
            ratingsCount: $reviewData['ratingsCount'] ?? [],
            lastReview: $lastReview,
            lastReviewDetails: $lastReviewDetails,
        );
    }

    /**
     * @param Review $review
     *
     * @return bool
    */
    public function hasDetails(Review $review): bool
    {
        return !$review->getDetails()->isEmpty();
    }

    /**
     * @param string[] $details
     *
     * @return string[]
    */
    public function limitDetails(array $details): array
    {
        return array_slice($details, 0, self::MAX_DETAILS);
    }

    /**
     * @param ProductVariant $variant
     *
     * @return int
    */
    private function assertVariantId(ProductVariant $variant): int
    {
        return IdAssertion::assert(
            $variant->getId(),
            'Variant ID',
            \LogicException::class,
        );
    }

    /**
     * @param User|null $user
     * @param int $variantId
     *
     * @return bool
    */
    private function canUserReview(?User $user, int $variantId): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        $hasPurchased = $this->orderItemRepository->hasPurchasedVariant($user, $variantId);

        return $hasPurchased && !$this->reviewRepository->existsByVariantAndUser($variantId, $user);
    }

    /**
     * @param Review[] $reviews
     *
     * @return ReviewObject[]
    */
    private function mapReviewsToObjects(array $reviews): array
    {
        return array_map(
            fn(Review $review): ReviewObject => new ReviewObject(
                review: $review,
                details: $review->getDetails()->toArray(),
                likesCount: 0,
                dislikesCount: 0,
            ),
            $reviews,
        );
    }

    /**
     * @param int $variantId
     *
     * @return array{
     *     lastReview: ReviewObject|null,
     *     details: ReviewDetail[]
     * }
    */
    private function getLastReviewWithDetails(int $variantId): array
    {
        $lastReview = $this->reviewRepository->getLastReviewByVariant($variantId);
        if (!$lastReview instanceof Review) {
            return [
                'lastReview' => null,
                'details'    => [],
            ];
        }

        $lastReviewObject = $this->mapSingleReviewToObject($lastReview);

        return [
            'lastReview' => $lastReviewObject,
            'details'    => $lastReviewObject->details,
        ];
    }

    /**
     * @param Review $review
     *
     * @return ReviewObject
    */
    private function mapSingleReviewToObject(Review $review): ReviewObject
    {
        return new ReviewObject(
            review: $review,
            details: $review->getDetails()->toArray(),
            likesCount: 0,
            dislikesCount: 0,
        );
    }
}
