<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject;

final readonly class OrderPersonalObject
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
    ) {}
}
