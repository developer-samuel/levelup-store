<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\User\Payload;

final readonly class ProfilePayload
{
    /**
     * @param array<string, int|string|null> $billing
     * @param array<string, int|string|null> $shipping
    */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public bool $useShipping,
        public array $billing,
        public array $shipping,
    ) {}
}
