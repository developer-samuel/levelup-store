<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Country\Service;

use App\Core\Domain\Segment\Country\ValueObject\CountryObject;

interface CountryCommandContract
{
    /**
     * @param CountryObject[] $countries
     *
     * @return void
    */
    public function saveCountries(array $countries): void;
}
