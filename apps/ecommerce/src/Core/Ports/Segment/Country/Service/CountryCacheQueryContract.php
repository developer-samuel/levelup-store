<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Country\Service;

use App\Core\Domain\Segment\Country\Entity\Country;

interface CountryCacheQueryContract
{
    /**
     * @return Country[]
    */
    public function getAllCountries(): array;
}
