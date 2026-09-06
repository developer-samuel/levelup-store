<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Event;

final readonly class ReviewRatingToggledEvent
{
    /**
     * @param int $variantId
     * @param int $reviewId
    */
    public function __construct(
        public int $variantId,
        public int $reviewId,
    ) {}
}
