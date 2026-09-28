<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\User\Utils;

use Kit\Utils\Shared\StringNormalizer;

final class NameFormatter
{
    /**
     * @param string $name
     *
     * @return string
    */
    public static function formatName(string $name): string
    {
        $normalized = trim($name);
        $normalized = StringNormalizer::toLowerCase($normalized);

        return ucfirst($normalized);
    }
}
