<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Payload;

final readonly class ReviewRatingPayload
{
    /**
     * @param int $reviewId
     * @param string|null $type
    */
    public function __construct(
        public int $reviewId,
        public ?string $type = null,
    ) {}
}
