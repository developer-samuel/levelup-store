<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\ValueObject;

final readonly class ReviewStatsObject
{
    /**
     * @param int $totalRatings
     * @param int $totalFeedbacks
    */
    public function __construct(
        public int $totalRatings,
        public int $totalFeedbacks,
    ) {}

    /**
     * @param array{
     *     totalRatings?: int,
     *     totalFeedbacks?: int
     * } $reviewData
     *
     * @return self
    */
    public static function fromArray(array $reviewData): self
    {
        return new self(
            totalRatings: $reviewData['totalRatings'] ?? 0,
            totalFeedbacks: $reviewData['totalFeedbacks'] ?? 0,
        );
    }

    /**
     * @return int
    */
    public function getTotalCount(): int
    {
        return $this->totalRatings + $this->totalFeedbacks;
    }
}
