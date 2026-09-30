<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class UpdateVerificationPayload
{
    /**
     * @param string $token
    */
    public function __construct(
        #[\SensitiveParameter] public string $token,
    ) {}
}
