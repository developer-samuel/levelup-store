<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class StringMacro
{
    /** @param array<string, bool|int|string|null> $options */
    public static function string(Table $table, string $name, int $length = 255, array $options = []): void
    {
        $options = array_merge(['length' => $length], $options);

        $table->addColumn($name, 'string', $options);
    }

    /** @param array<string, bool|int|string|null> $options */
    public static function text(Table $table, string $name, array $options = []): void
    {
        $table->addColumn($name, 'text', $options);
    }
}
