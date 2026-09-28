<?php

declare(strict_types=1);

namespace Database\Macros\Integer;

use Doctrine\DBAL\Schema\Table;

use Database\Macros\ColumnOptionsMacro;

final class SmallIntegerMacro
{
    /**
     * Add a small integer column to the table with dynamic parameters.
     * Small Integer range: -32,768 to 32,767.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function smallInteger(Table $table, string $column, ?int $default = 0, array $options = []): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);

        $table->addColumn($column, 'smallint', $options);
    }

    /**
     * Add an unsigned small integer column to the table with dynamic parameters.
     * Unsigned Small Integer range: 0 to 65,535.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function unsignedSmallInteger(Table $table, string $column, ?int $default = 0, array $options = ['unsigned' => true]): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'smallint', $options);
    }

    /**
     * Add a signed small integer column to the table with dynamic parameters.
     * Signed Small Integer range: -32,768 to 32,767.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function signedSmallInteger(Table $table, string $column, ?int $default = 0, array $options = ['signed' => true]): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'smallint', $options);
    }
}
