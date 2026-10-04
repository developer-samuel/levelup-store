<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Country\Utils;

use App\Core\Domain\Segment\Country\Entity\Country;

/**
 * @phpstan-type CountryData array{
 *     id: int,
 *     name: string
 * }
*/
final class CountryTransformer
{
    /** @return CountryData|null */
    public static function transformCountry(?Country $country): ?array
    {
        if ($country === null) {
            return null;
        }

        return self::extractData($country);
    }

    /** @return CountryData */
    private static function extractData(Country $country): array
    {
        $id = $country->getId();
        $name = $country->getName();

        return [
            'id'   => $id,
            'name' => $name,
        ];
    }
}
