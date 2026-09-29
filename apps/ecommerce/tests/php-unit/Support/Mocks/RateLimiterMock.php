<?php

declare(strict_types=1);

namespace Tests\Support\Mocks;

use PHPUnit\Framework\MockObject\MockObject;

use App\Core\Ports\Shared\RateLimiter\RateLimiterContract;

trait RateLimiterMock
{
    private function createRateLimiterMock(): RateLimiterContract&MockObject
    {
        return $this->createMock(RateLimiterContract::class);
    }
}
