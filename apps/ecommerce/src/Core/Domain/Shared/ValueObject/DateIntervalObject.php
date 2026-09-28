<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\ValueObject;

final readonly class DateIntervalObject
{
    /**
     * @param \DateTimeImmutable $start
     * @param \DateTimeImmutable $end
    */
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {}
}
