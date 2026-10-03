<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Event;

final readonly class ReviewRatingToggledEvent
{
    public function __construct(
        public int $variantId,
        public int $reviewId,
    ) {}
}
