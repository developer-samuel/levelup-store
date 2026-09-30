<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Categories;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateCategoriesTable
{
    /**
     * Build the entire schema definition for the 'categories' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('categories');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
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
        self::addAdditionalColumn($table);
        self::addTimestamp($table);
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
        StringMacro::string($table, 'name', 20);
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
        UniqueKeyMacro::add($table, ['name'], 'unique_name_categories');
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
