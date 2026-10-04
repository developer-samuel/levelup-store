<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\ValueObject;

final readonly class JwtTokenObject
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
    ) {}
}
