<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Handler\Command;

use App\Core\Domain\Segment\Review\Payload\ReviewCreatePayload;

interface ReviewCommandHandlerContract
{
    /** @return array<string, mixed> */
    public function handle(ReviewCreatePayload $payload): array;
}
