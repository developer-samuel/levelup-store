<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Constants;

final class AddressLimitsConstants
{
    public const COUNTRY_MAX = 100;
    public const STREET_MAX = 200;
    public const POSTAL_CODE_MIN = 3;
    public const POSTAL_CODE_MAX = 15;
    public const CITY_MIN = 2;
    public const CITY_MAX = 10;
}
