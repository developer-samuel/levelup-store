<?php

declare(strict_types=1);

namespace App\Core\Domain\Shared\ValueObject;

final readonly class AddressObject
{
    /**
     * @param string $country
     * @param string $street
     * @param string $postalCode
     * @param string $city
     * @param bool|null $sendShipping
    */
    public function __construct(
        public string $country,
        public string $street,
        public string $postalCode,
        public string $city,
        public ?bool $sendShipping = null,
    ) {}
}
