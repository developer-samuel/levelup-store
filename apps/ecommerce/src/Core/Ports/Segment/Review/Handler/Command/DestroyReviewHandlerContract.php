<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Handler\Command;

use App\Core\Domain\Segment\Review\Payload\ReviewDestroyPayload;

interface DestroyReviewHandlerContract
{
    /**
     * @param ReviewDestroyPayload $payload
     *
     * @return array<string, mixed>
    */
    public function handle(ReviewDestroyPayload $payload): array;
}
