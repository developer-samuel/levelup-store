<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Auth\ValueObject\JwtTokenObject
};

interface UpdateVerificationHandlerContract
{
    /**
     * @param UpdateVerificationPayload $payload
     *
     * @return JwtTokenObject|null
    */
    public function handle(UpdateVerificationPayload $payload): ?JwtTokenObject;
}
