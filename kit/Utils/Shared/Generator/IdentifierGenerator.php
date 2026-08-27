<?php
declare(strict_types=1);

namespace Kit\Utils\Shared\Generator;

use Kit\Constants\CharacterConstants;

final class IdentifierGenerator
{
     /**
     * @param string $name
     * @param int $lettersPerWord
     *
     * @return string
    */
    public static function generatePrefix(string $name, int $lettersPerWord = 1): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];
        $prefix = '';

        foreach ($words as $word) {
            $prefix .= strtoupper(substr($word, 0, $lettersPerWord));
        }

        return $prefix;
    }

    /**
     * @param int $length
     *
     * @return string
    */
    public static function generateRandomAlphanumeric(int $length): string
    {
        $chars = CharacterConstants::UPPERCASE . CharacterConstants::DIGITS;
        $repeatTimes = (int) ceil($length / strlen($chars));

        return substr(str_shuffle(str_repeat($chars, $repeatTimes)), 0, $length);
    }
}
