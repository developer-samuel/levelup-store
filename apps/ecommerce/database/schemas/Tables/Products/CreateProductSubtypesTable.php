<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Products;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\IntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro
};

final class CreateProductSubtypesTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('product_subtypes');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumns($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        IntegerMacro::unsignedInteger($table, 'product_id');
        IntegerMacro::unsignedInteger($table, 'subtype_id');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['product_id'], 'idx_product_subtypes_product_id');
        IndexMacro::add($table, ['subtype_id'], 'idx_product_subtypes_subtype_id');
    }

    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'products', ['product_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'subtypes', ['subtype_id'], ['id']);
    }
}
