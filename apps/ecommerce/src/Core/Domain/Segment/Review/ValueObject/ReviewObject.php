<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\ValueObject;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewDetail
};

final readonly class ReviewObject
{
    /**
     * @param Review $review
     * @param ReviewDetail[] $details
     * @param int $likesCount
     * @param int $dislikesCount
     * @param string|null $userRatingType
    */
    public function __construct(
        public Review $review,
        public array $details,
        public int $likesCount,
        public int $dislikesCount,
        public ?string $userRatingType = null,
    ) {}
}
