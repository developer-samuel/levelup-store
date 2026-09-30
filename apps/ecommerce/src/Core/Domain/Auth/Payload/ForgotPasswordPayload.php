<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class ForgotPasswordPayload
{
    /**
     * @param string $email
    */
    public function __construct(
        public string $email,
    ) {}
}
