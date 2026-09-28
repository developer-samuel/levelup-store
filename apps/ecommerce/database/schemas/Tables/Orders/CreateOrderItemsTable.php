<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Orders;

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
    /**
     * Build the entire schema definition for the 'order_items' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('order_items');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
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
        IdMacro::addBigIdColumn($table);
        self::addStandardColumns($table);
        self::addAdditionalColumn($table);
    }

    /**
     * Add order_id, variant_id and ean_id columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
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

    /**
     * Add additional column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumn(Table $table): void
    {
        DecimalMacro::add($table, 'price');
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
        UniqueKeyMacro::add($table, ['ean_id'], 'unique_ean_id_order_items');
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
        IndexMacro::add($table, ['order_id'], 'idx_order_items_order_id');
        IndexMacro::add($table, ['variant_id'], 'idx_order_items_variant_id');
        IndexMacro::add($table, ['ean_id'], 'idx_order_items_ean_id');
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
        ForeignKeyMacro::addForeignKeys($table, 'orders', ['order_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variant_eans', ['ean_id'], ['id']);
    }
}
