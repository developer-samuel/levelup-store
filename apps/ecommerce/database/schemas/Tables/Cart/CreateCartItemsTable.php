<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Cart;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\Integer\IntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro
};

final class CreateCartItemsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('cart_items');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addStandardColumns($table);
        self::addTimestamp($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'cart_id');
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['cart_id'], 'idx_cart_items_cart_id');
        IndexMacro::add($table, ['variant_id'], 'idx_cart_items_variant_id');
    }

    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'carts', ['cart_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }
}
