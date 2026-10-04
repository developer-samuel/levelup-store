<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class UpdateVerificationPayload
{
    public function __construct(
        #[\SensitiveParameter] public string $token,
    ) {}
}
