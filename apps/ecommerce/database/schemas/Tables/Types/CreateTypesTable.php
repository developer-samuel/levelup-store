<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Types;

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
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateTypesTable
{
    /**
     * Build the entire schema definition for the 'types' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('types');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
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
        IdMacro::addSmallIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumn($table);
        self::addTimestamp($table);
    }

    /**
     * Add category_id column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumn(Table $table): void
    {
        SmallIntegerMacro::unsignedSmallInteger($table, 'category_id');
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
     * Add unique index to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['name'], 'unique_name_types');
    }

    /**
     * Add predefined index to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['category_id'], 'idx_types_category_id');
    }

    /**
     * Add foreign key to the table using ForeignKeyMacro with dynamic parameters.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'categories', ['category_id'], ['id']);
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
