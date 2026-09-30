<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\Payload;

final readonly class SignupPayload
{
    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $password
     * @param string $passwordConfirmation
    */
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        #[\SensitiveParameter] public string $password,
        #[\SensitiveParameter] public string $passwordConfirmation,
    ) {}
}
