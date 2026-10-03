<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class ResetPasswordPayload
{
    public function __construct(
        #[\SensitiveParameter] public string $token,
        #[\SensitiveParameter] public string $password,
        #[\SensitiveParameter] public string $passwordConfirmation,
    ) {}
}
