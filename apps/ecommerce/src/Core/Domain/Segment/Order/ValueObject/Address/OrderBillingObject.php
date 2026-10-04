<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Order\ValueObject\Address;

final readonly class OrderBillingObject
{
    public function __construct(
        public int $country,
        public string $street,
        public string $postalCode,
        public string $city,
    ) {}
}
