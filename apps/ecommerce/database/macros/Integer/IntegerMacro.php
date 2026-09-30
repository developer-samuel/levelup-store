<?php

declare(strict_types=1);

namespace Database\Macros\Integer;

use Doctrine\DBAL\Schema\Table;

use Database\Macros\ColumnOptionsMacro;

final class IntegerMacro
{
    /**
     * Add an integer column to the table with dynamic parameters.
     * Integer range: -2,147,483,648 to 2,147,483,647.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function integer(Table $table, string $column, ?int $default = 0, array $options = []): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'integer', $options);
    }

    /**
     * Add an unsigned integer column to the table with dynamic parameters.
     * Unsigned Integer range: 0 to 4,294,967,295.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function unsignedInteger(Table $table, string $column, ?int $default = 0, array $options = ['unsigned' => true]): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'integer', $options);
    }

    /**
     * Add a signed integer column to the table with dynamic parameters.
     * Signed Integer range: -2,147,483,648 to 2,147,483,647.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function signedInteger(Table $table, string $column, ?int $default = 0, array $options = ['signed' => true]): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'integer', $options);
    }
}
