<?php

declare(strict_types=1);

namespace App\Core\Ports\Auth\Handler\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Auth\ValueObject\JwtTokenObject
};

interface UpdateVerificationHandlerContract
{
    public function handle(UpdateVerificationPayload $payload): ?JwtTokenObject;
}
