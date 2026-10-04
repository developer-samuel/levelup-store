<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Country\ValueObject;

final readonly class CountryObject
{
    public function __construct(
        public string $code,
        public string $name,
    ) {}
}
