<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\ValueObject;

use App\Core\Domain\{
    Segment\Review\Entity\ReviewDetail
};

final readonly class ReviewListObject
{
    /**
     * @param ReviewObject[] $reviews
     * @param array<string, int> $ratingsCount
     * @param ReviewDetail[] $lastReviewDetails
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
