<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Handler\Query;

use App\Core\Domain\Segment\Review\ValueObject\ReviewListWithVariantObject;

interface ReviewListQueryHandlerContract
{
    public function handle(string $url): ?ReviewListWithVariantObject;
}
