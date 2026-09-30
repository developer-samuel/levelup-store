<?php

declare(strict_types=1);

namespace Database\Macros\Integer;

use Doctrine\DBAL\Schema\Table;

use Database\Macros\ColumnOptionsMacro;

final class BigIntegerMacro
{
    /**
     * Add a big integer column to the table with dynamic parameters.
     * Big Integer range: -9.223 * 10^18 to 9.223 * 10^18.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function bigInteger(Table $table, string $column, ?int $default = 0, array $options = []): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'bigint', $options);
    }

    /**
     * Add an unsigned big integer column to the table with dynamic parameters.
     * Unsigned Big Integer range: 0 to 1.844 * 10^19.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function unsignedBigInteger(Table $table, string $column, ?int $default = 0, array $options = ['unsigned' => true]): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'bigint', $options);
    }

    /**
     * Add a signed big integer column to the table with dynamic parameters.
     * Signed Big Integer range: -9.223 * 10^18 to 9.223 * 10^18.
     *
     * @param Table $table
     * @param string $column
     * @param int|null $default
     * @param array<string, bool|int|string> $options
     *
     * @return void
    */
    public static function signedBigInteger(Table $table, string $column, ?int $default = 0, array $options = ['signed' => true]): void
    {
        $options = ColumnOptionsMacro::mergeOptions($options, $default);
        $table->addColumn($column, 'bigint', $options);
    }
}
