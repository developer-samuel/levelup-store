<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Utils\Mapper;

final class ArrayMapper
{
    /**
     * @param string[] $values
     * @param string[] $keys
     *
     * @return string[]
    */
    public static function mapValuesToKeys(array $values, array $keys): array
    {
        $result = [];

        foreach ($keys as $key => $mappedKey) {
            $result[$mappedKey] = $values[$key] ?? '';
        }

        return $result;
    }

    /**
     * @param string[] $keys
     *
     * @return string[]
    */
    public static function emptyByKeys(array $keys): array
    {
        return array_fill_keys($keys, '');
    }
}
