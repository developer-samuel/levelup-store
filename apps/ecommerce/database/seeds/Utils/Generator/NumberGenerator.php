<?php

declare(strict_types=1);

namespace Database\Seeds\Utils\Generator;

use Kit\Constants\CharacterConstants;

final class NumberGenerator
{
    /**
     * @param int $length
     *
     * @return string
    */
    public static function generate(int $length = 6): string
    {
        return self::generateRandomNumber($length);
    }

    /**
     * @param int $length
     *
     * @return string
    */
    private static function generateRandomNumber(int $length): string
    {
        $digits = CharacterConstants::DIGITS;
        $digitsLength = strlen($digits);

        if ($digitsLength === 0) {
            return '';
        }

        return self::buildRandomNumber($digits, $digitsLength, $length);
    }

    /**
     * @param string $digits
     * @param int $digitsLength
     * @param int $length
     *
     * @return string
    */
    private static function buildRandomNumber(string $digits, int $digitsLength, int $length): string
    {
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $digitsLength - 1);
            $randomNumber .= $digits[$randomIndex];
        }

        return $randomNumber;
    }
}
