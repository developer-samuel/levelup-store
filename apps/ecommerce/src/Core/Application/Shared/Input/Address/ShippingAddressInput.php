<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Input\Address;

use App\Core\Application\{
    Shared\Constants\AddressLimitsConstants,
    Shared\Constraint\Length\MaxLengthConstraint,
    Shared\Constraint\Length\MinLengthConstraint
};

trait ShippingAddressInput
{
    #[MaxLengthConstraint('Country', AddressLimitsConstants::COUNTRY_MAX)]
    public int $shipping_country;

    #[MaxLengthConstraint('Street', AddressLimitsConstants::STREET_MAX)]
    public string $shipping_street;

    #[MinLengthConstraint('Postal Code', AddressLimitsConstants::POSTAL_CODE_MIN)]
    #[MaxLengthConstraint('Postal Code', AddressLimitsConstants::POSTAL_CODE_MAX)]
    public string $shipping_postal_code;

    #[MinLengthConstraint('City', AddressLimitsConstants::CITY_MIN)]
    #[MaxLengthConstraint('City', AddressLimitsConstants::CITY_MAX)]
    public string $shipping_city;
}
