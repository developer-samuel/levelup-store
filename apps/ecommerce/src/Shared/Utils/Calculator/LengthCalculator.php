<?php

declare(strict_types=1);

namespace App\Shared\Utils\Calculator;

final class LengthCalculator
{
    /**
     * @param mixed $value
     *
     * @return int|null
    */
    public static function getLength(mixed $value): ?int
    {
        if (is_string($value)) {
            return mb_strlen($value);
        }

        if (is_int($value)) {
            return mb_strlen((string) $value);
        }

        return null;
    }
}
