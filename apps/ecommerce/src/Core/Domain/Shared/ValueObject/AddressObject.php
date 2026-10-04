<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\ValueObject;

final readonly class AddressObject
{
    public function __construct(
        public string $country,
        public string $street,
        public string $postalCode,
        public string $city,
        public ?bool $sendShipping = null,
    ) {}
}
