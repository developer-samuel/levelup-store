<?php

declare(strict_types=1);

namespace Tests\Support\Traits;

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Ports\Shared\RateLimiter\RateLimiterContract;

trait RateLimiterMockTrait
{
    private function createRateLimiterMock(): RateLimiterContract&MockObject
    {
        return $this->createMock(RateLimiterContract::class);
    }
}
