<?php

declare(strict_types=1);

namespace Packages\Kit\Utils\Shared;

final class StringNormalizer
{
    public static function normalize(string $value): string
    {
        $value = trim($value);
        $value = self::replaceSpacesAndPluses($value);

        return self::toLowerCase($value);
    }

    public static function toLowerCase(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    public static function toUpperCase(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    public static function capitalizeWords(string $value): string
    {
        return ucwords(mb_strtolower($value, 'UTF-8'));
    }

    public static function replaceSpacesWithDash(string $value): string
    {
        return str_replace(' ', '-', $value);
    }

    public static function replaceUnderscoresWithSpaces(string $value): string
    {
        return str_replace('_', ' ', $value);
    }

    public static function capitalizeAndReplaceUnderscoresWithSpaces(string $value): string
    {
        return self::capitalizeWords(
            self::replaceUnderscoresWithSpaces($value),
        );
    }

    private static function replaceSpacesAndPluses(string $value): string
    {
        return str_replace([' ', '+'], '-', $value);
    }
}
