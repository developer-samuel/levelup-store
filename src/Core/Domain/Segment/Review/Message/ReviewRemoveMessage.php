<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Review\Message;

final readonly class ReviewRemoveMessage
{
    /**
     * @param int $reviewId
    */
    public function __construct(
        public int $reviewId,
    ) {}
}
