<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Country\ValueObject;

final readonly class CountryObject
{
    /**
     * @param string $code
     * @param string $name
    */
    public function __construct(
        public string $code,
        public string $name,
    ) {}
}
