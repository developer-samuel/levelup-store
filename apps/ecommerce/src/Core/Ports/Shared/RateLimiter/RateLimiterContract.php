<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\RateLimiter;


interface RateLimiterContract
{
    public function track(): void;
    public function reset(): void;
}
