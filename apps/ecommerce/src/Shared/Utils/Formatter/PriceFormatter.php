<?php

declare(strict_types=1);

namespace App\Shared\Utils\Formatter;

final class PriceFormatter
{
    /**
     * @param float $price
     *
     * @return string
    */
    public static function format(float $price): string
    {
        $decimals = self::getDecimalPlaces($price);
        $formattedPrice = self::formatNumber($price, $decimals);

        return $formattedPrice . ' €';
    }

    /**
     * @param float $price
     *
     * @return int
    */
    private static function getDecimalPlaces(float $price): int
    {
        return (fmod($price, 1.0) === 0.0) ? 0 : 2;
    }

    /**
     * @param float $price
     * @param int $decimals
     *
     * @return string
    */
    private static function formatNumber(float $price, int $decimals): string
    {
        return number_format($price, $decimals, '.', '');
    }
}
