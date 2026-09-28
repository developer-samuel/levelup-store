<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Handler\Command;

use App\Core\Domain\Segment\Review\Payload\ReviewRatingPayload;

interface ToggleReviewRatingHandlerContract
{
    /**
     * @param ReviewRatingPayload $payload
     *
     * @return array<string, mixed>
     */
    public function handle(ReviewRatingPayload $payload): array;
}
