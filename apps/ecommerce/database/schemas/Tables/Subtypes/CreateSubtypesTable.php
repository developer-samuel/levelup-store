<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Subtypes;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\SmallIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro
};

final class CreateSubtypesTable
{
    /**
     * Build the entire schema definition for the 'subtypes' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('subtypes');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
        self::addCheckConstraint($table);
    }

    /**
     * Add columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumns($table);
        self::addAdditionalColumn($table);
        self::addTimestamp($table);
    }

    /**
     * Add category_id and type_id columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumns(Table $table): void
    {
        SmallIntegerMacro::unsignedSmallInteger($table, 'category_id');
        SmallIntegerMacro::unsignedSmallInteger($table, 'type_id');
    }

    /**
     * Add additional column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumn(Table $table): void
    {
        StringMacro::string($table, 'name', 100);
    }

    /**
     * Add timestamp column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
    }

    /**
     * Add predefined indexes to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['category_id'], 'idx_subtypes_category_id');
        IndexMacro::add($table, ['type_id'], 'idx_subtypes_type_id');
    }

    /**
     * Add foreign keys to the table using ForeignKeyMacro with dynamic parameters.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'categories', ['category_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'types', ['type_id'], ['id']);
    }

    /**
     * Add check constraint to ensure data validity.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addCheckConstraint(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_name_min_length',
            'CHAR_LENGTH(name) >= 2',
        );
    }
}
