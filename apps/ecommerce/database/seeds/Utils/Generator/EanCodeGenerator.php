<?php
declare(strict_types=1);

namespace Database\Seeds\Utils\Generator;

final class EanCodeGenerator
{
    public static function generateEan13(): string
    {
        $baseDigits = self::generateBaseDigits(12);
        $checkDigit = self::calculateCheckDigit($baseDigits);

        return $baseDigits . $checkDigit;
    }

    private static function generateBaseDigits(int $length): string
    {
        return NumberGenerator::generate($length);
    }

    private static function calculateCheckDigit(string $digits): int
    {
        $sum = 0;
        $digitArray = str_split($digits);

        foreach ($digitArray as $i => $d) {
            $sum += (int) $d * ($i % 2 === 0 ? 1 : 3);
        }

        return (10 - ($sum % 10)) % 10;
    }
}
