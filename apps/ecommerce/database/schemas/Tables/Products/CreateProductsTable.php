<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Products;

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

final class CreateProductsTable
{
    /**
     * Build the entire schema definition for the 'products' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('products');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndexes($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
        self::addCheckConstraints($table);
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
        self::addAdditionalColumns($table);
        self::addTimestamps($table);
    }

    /**
     * Add category_id, type_id and brand_id columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumns(Table $table): void
    {
        SmallIntegerMacro::unsignedSmallInteger($table, 'category_id');
        SmallIntegerMacro::unsignedSmallInteger($table, 'type_id');
        SmallIntegerMacro::unsignedSmallInteger($table, 'brand_id');
    }

    /**
     * Add additional columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'catalog_code', 50);
        StringMacro::string($table, 'name');
    }

    /**
     * Add timestamps columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    /**
     * Add unique indexes to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addUniqueIndexes(Table $table): void
    {
        UniqueKeyMacro::add($table, ['catalog_code'], 'unique_catalog_code_products');
        UniqueKeyMacro::add($table, ['name'], 'unique_name_products');
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
        IndexMacro::add($table, ['category_id'], 'idx_products_category_id');
        IndexMacro::add($table, ['type_id'], 'idx_products_type_id');
        IndexMacro::add($table, ['brand_id'], 'idx_products_brand_id');
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
        ForeignKeyMacro::addForeignKeys($table, 'brands', ['brand_id'], ['id']);
    }

    /**
     * Add check constraints to ensure data validity.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_catalog_code_min_length',
            'CHAR_LENGTH(catalog_code) >= 2',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_name_min_length',
            'CHAR_LENGTH(name) >= 2',
        );
    }
}
