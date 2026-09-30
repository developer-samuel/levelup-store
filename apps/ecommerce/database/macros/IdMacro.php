<?php

declare(strict_types=1);

namespace Database\Macros;

use Doctrine\DBAL\Schema\Table;

use Database\Validators\IntegerTypeValidator;

final class IdMacro
{
    /**
     * Adds a dynamic ID column with appropriate type and auto-increment settings.
     * Integer range: -2,147,483,648 to 2,147,483,647.
     *
     * @param Table $table
     * @param string $column Name of the column (default 'id')
     * @param string $type Type of the ID column: 'integer', 'bigint', 'smallint'
     *
     * @return void
    */
    public static function addIdColumn(Table $table, string $column = 'id', string $type = 'integer'): void
    {
        IntegerTypeValidator::validateIdType($type);
        $options = ['autoincrement' => true, 'unsigned' => true];

        $table->addColumn($column, $type, $options);
    }

    /**
     * Add a dynamic Big ID column with auto-increment and unsigned option.
     * Big Integer range: -9.223 * 10^18 to 9.223 * 10^18.
     *
     * @param Table $table
     * @param string $column Name of the column (default 'id')
     *
     * @return void
    */
    public static function addBigIdColumn(Table $table, string $column = 'id'): void
    {
        self::addIdColumn($table, $column, 'bigint');
    }

    /**
     * Add a dynamic Small ID column with auto-increment and unsigned option.
     * Small Integer range: -32,768 to 32,767.
     *
     * @param Table $table
     * @param string $column Name of the column (default 'id')
     *
     * @return void
    */
    public static function addSmallIdColumn(Table $table, string $column = 'id'): void
    {
        self::addIdColumn($table, $column, 'smallint');
    }
}
