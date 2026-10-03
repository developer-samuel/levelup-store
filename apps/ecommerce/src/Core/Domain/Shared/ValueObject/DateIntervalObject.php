<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\ValueObject;

final readonly class DateIntervalObject
{
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {}
}
