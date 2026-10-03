<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class ForgotPasswordPayload
{
    public function __construct(
        public string $email,
    ) {}
}
