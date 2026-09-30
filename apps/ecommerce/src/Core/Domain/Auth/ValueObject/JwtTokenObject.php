<?php

declare(strict_types=1);

namespace App\Core\Domain\Auth\ValueObject;

final readonly class JwtTokenObject
{
    /**
     * @param string $accessToken
     * @param string $refreshToken
    */
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
    ) {}
}
