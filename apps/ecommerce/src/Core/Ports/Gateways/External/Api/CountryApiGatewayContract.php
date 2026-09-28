<?php

declare(strict_types=1);

namespace App\Core\Ports\Gateways\External\Api;

use App\Core\Domain\Segment\Country\ValueObject\CountryObject;

interface CountryApiGatewayContract
{
    /**
     * @return CountryObject[]|null
    */
    public function getAllCountries(): ?array;

    /**
     * @param string $code
     *
     * @return bool
    */
    public function countryExists(string $code): bool;
}
