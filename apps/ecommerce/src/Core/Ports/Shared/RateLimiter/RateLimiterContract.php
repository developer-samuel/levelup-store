<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\RateLimiter;

use App\Core\Domain\Shared\Exception\TooManyRequestsException;

interface RateLimiterContract
{
    /**
     * @return void
     *
     * @throws TooManyRequestsException
    */
    public function track(): void;

    /**
     * @return void
    */
    public function reset(): void;
}
