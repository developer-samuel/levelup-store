<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Order;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\DecimalMacro,
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\Integer\IntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\UniqueKeyMacro
};

final class CreateOrderItemsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('order_items');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addStandardColumns($table);
        self::addAdditionalColumn($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'order_id');
        IntegerMacro::unsignedInteger($table, 'variant_id');

        BigIntegerMacro::unsignedBigInteger(
            $table,
            'ean_id',
            null,
            ['unsigned' => true, 'notnull' => false],
        );
    }

    private static function addAdditionalColumn(Table $table): void
    {
        DecimalMacro::add($table, 'price');
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['ean_id'], 'unique_ean_id_order_items');
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['order_id'], 'idx_order_items_order_id');
        IndexMacro::add($table, ['variant_id'], 'idx_order_items_variant_id');
        IndexMacro::add($table, ['ean_id'], 'idx_order_items_ean_id');
    }

    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'orders', ['order_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variant_eans', ['ean_id'], ['id']);
    }
}
