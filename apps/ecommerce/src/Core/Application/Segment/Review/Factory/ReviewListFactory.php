<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Factory;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Review\Entity\Review,
    Segment\Review\ValueObject\ReviewListObject,
    Segment\Review\ValueObject\ReviewListWithVariantObject,
    Segment\Review\ValueObject\ReviewObject,
    Segment\Review\ValueObject\ReviewStatsObject,
};

/**
 * @phpstan-type ReviewWithRatings array{
 *     review: Review,
 *     likesCount: int,
 *     dislikesCount: int,
 *     userRatingType: string|null
 * }
 * @phpstan-type ReviewData array{
 *     average: float,
 *     totalRatings: int,
 *     totalFeedbacks: int,
 *     ratingsCount: array<string,int>
 * }
*/
final class ReviewListFactory
{
    /**
     * @param array<int, ReviewWithRatings> $reviewsWithRatings
     * @param bool $reviewExists
     * @param ReviewData $reviewData
     * @param ProductVariant $variant
     *
     * @return ReviewListWithVariantObject
    */
    public function fromObject(
        array $reviewsWithRatings,
        bool $reviewExists,
        array $reviewData,
        ProductVariant $variant,
    ): ReviewListWithVariantObject {
        $reviews = $this->mapReviewsWithRatings($reviewsWithRatings);

        $reviewList = $this->createReviewList(
            $reviews,
            $reviewExists,
            $reviewData,
        );

        return new ReviewListWithVariantObject($reviewList, $variant);
    }

    /**
     * @param array<int, ReviewWithRatings> $reviewsWithRatings
     *
     * @return ReviewObject[]
    */
    private function mapReviewsWithRatings(array $reviewsWithRatings): array
    {
        return array_map(
            fn(array $item): ReviewObject => new ReviewObject(
                review: $item['review'],
                details: $item['review']->getDetails()->toArray(),
                likesCount: $item['likesCount'],
                dislikesCount: $item['dislikesCount'],
                userRatingType: $item['userRatingType'] ?? null,
            ),
            $reviewsWithRatings,
        );
    }

    /**
     * @param ReviewObject[] $reviews
     * @param bool $reviewExists
     * @param ReviewData $reviewData
     *
     * @return ReviewListObject
    */
    private function createReviewList(
        array $reviews,
        bool $reviewExists,
        array $reviewData,
    ): ReviewListObject {
        $stats = ReviewStatsObject::fromArray($reviewData);

        return new ReviewListObject(
            reviewExists: $reviewExists,
            reviews: $reviews,
            averageRating: $reviewData['average'] ?? 0.0,
            totalRatings: $stats->totalRatings,
            totalFeedbacks: $stats->totalFeedbacks,
            totalCount: $stats->getTotalCount(),
            ratingsCount: $reviewData['ratingsCount'] ?? [],
        );
    }
}
