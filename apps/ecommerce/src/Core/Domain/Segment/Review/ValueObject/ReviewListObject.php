<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\ValueObject;

use App\Core\Domain\{
    Segment\Review\Entity\ReviewDetail
};

final readonly class ReviewListObject
{
    /**
     * @param bool $reviewExists
     * @param ReviewObject[] $reviews
     * @param float $averageRating
     * @param int $totalRatings
     * @param int $totalFeedbacks
     * @param int $totalCount
     * @param array<string, int> $ratingsCount
     * @param ReviewDetail[] $lastReviewDetails
     * @param ReviewObject|null $lastReview
    */
    public function __construct(
        public bool $reviewExists,
        public array $reviews,
        public float $averageRating,
        public int $totalRatings,
        public int $totalFeedbacks,
        public int $totalCount,
        public array $ratingsCount,
        public array $lastReviewDetails = [],
        public ?ReviewObject $lastReview = null,
    ) {}
}
