<?php
declare(strict_types=1);

namespace Packages\Kit\Utils\Shared;

use Packages\Kit\Constants\CharacterConstants;

final class IdentifierGenerator
{
    public static function generatePrefix(string $name, int $lettersPerWord = 1): string
    {
        $split = preg_split('/\s+/', trim($name));
        $words = $split !== false ? $split : [];
        $prefix = '';

        foreach ($words as $word) {
            $prefix .= strtoupper(substr($word, 0, $lettersPerWord));
        }

        return $prefix;
    }

    public static function generateRandomAlphanumeric(int $length): string
    {
        $chars = CharacterConstants::UPPERCASE . CharacterConstants::DIGITS;
        $repeatTimes = (int) ceil($length / strlen($chars));

        return substr(str_shuffle(str_repeat($chars, $repeatTimes)), 0, $length);
    }
}
