<?php

declare(strict_types=1);

namespace Tests\Support\Provides;

trait DateRange
{
    /**
     * @return array{\DateTimeImmutable, \DateTimeImmutable}
    */
    private function dateRangeNow(): array
    {
        return [
            new \DateTimeImmutable('-1 minute'),
            new \DateTimeImmutable('+1 minute'),
        ];
    }

    /**
     * @return array{\DateTimeImmutable, \DateTimeImmutable}
    */
    private function dateRangeFuture(): array
    {
        return [
            new \DateTimeImmutable('+1 hour'),
            new \DateTimeImmutable('+2 hours'),
        ];
    }
}
