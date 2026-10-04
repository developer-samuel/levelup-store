<?php

declare(strict_types=1);

namespace App\Shared\Utils\Formatter;

final class PriceFormatter
{
    public static function format(float $price): string
    {
        $decimals = self::getDecimalPlaces($price);
        $formattedPrice = self::formatNumber($price, $decimals);

        return $formattedPrice . ' €';
    }

    private static function getDecimalPlaces(float $price): int
    {
        return (fmod($price, 1.0) === 0.0) ? 0 : 2;
    }

    private static function formatNumber(float $price, int $decimals): string
    {
        return number_format($price, $decimals, '.', '');
    }
}
