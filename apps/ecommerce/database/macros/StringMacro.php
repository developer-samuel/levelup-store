<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

final class StringMacro
{
    /**
     * @param Table $table
     * @param string $name
     * @param int $length
     * @param array<string, bool|int|string|null> $options
     *
     * @return void
    */
    public static function string(Table $table, string $name, int $length = 255, array $options = []): void
    {
        $options = array_merge(['length' => $length], $options);

        $table->addColumn($name, 'string', $options);
    }

    /**
     * @param Table $table
     * @param string $name
     * @param array<string, bool|int|string|null> $options
     *
     * @return void
    */
    public static function text(Table $table, string $name, array $options = []): void
    {
        $table->addColumn($name, 'text', $options);
    }
}
