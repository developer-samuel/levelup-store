<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Payload;

final readonly class ReviewDestroyPayload
{
    /**
     * @param int $reviewId
    */
    public function __construct(
        public int $reviewId,
    ) {}
}
