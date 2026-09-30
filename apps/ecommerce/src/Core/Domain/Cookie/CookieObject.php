<?php

declare(strict_types=1);

namespace App\Core\Domain\Cookie;

final readonly class CookieObject
{
    /**
     * @param string $name
     * @param string $value
     * @param int $expiresAt
     * @param string $path
     * @param bool $secure
     * @param bool $httpOnly
     * @param string|null $sameSite
    */
    public function __construct(
        public string $name,
        public string $value,
        public int $expiresAt,
        public string $path,
        public bool $secure,
        public bool $httpOnly,
        public ?string $sameSite = null,
    ) {}
}
