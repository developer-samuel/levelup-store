<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Utils;

use Packages\Kit\Constants\CharacterConstants;

final class CodeGenerator
{
    public static function generateUnique(int $length = 20): string
    {
        return self::generateRandomString($length);
    }

    public static function getAllCharacters(): string
    {
        return CharacterConstants::DIGITS . CharacterConstants::LOWERCASE . CharacterConstants::UPPERCASE;
    }

    private static function generateRandomString(int $length): string
    {
        $characters = self::getAllCharacters();
        $charactersLength = strlen($characters);

        if ($charactersLength === 0) {
            return '';
        }

        return self::buildRandomString($characters, $charactersLength, $length);
    }

    private static function buildRandomString(string $characters, int $charactersLength, int $length): string
    {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }
}
