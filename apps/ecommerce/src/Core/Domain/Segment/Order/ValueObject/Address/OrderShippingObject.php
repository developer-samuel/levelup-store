<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Address;

final readonly class OrderShippingObject
{
    /**
     * @param int $country
     * @param string $street
     * @param string $postalCode
     * @param string $city
    */
    public function __construct(
        public int $country,
        public string $street,
        public string $postalCode,
        public string $city,
    ) {}
}
