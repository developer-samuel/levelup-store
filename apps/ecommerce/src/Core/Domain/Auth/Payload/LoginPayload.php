<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class LoginPayload
{
    /**
     * @param string $email
     * @param string $password
    */
    public function __construct(
        public string $email,
        #[\SensitiveParameter] public string $password,
    ) {}
}
