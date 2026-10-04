<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Wishlist;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\IntegerMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro,
};

final class CreateWishlistsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('wishlists');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumns($table);
        self::addTimestamp($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['user_id'], 'idx_wishlists_user_id');
        IndexMacro::add($table, ['variant_id'], 'idx_wishlists_variant_id');
    }

    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }
}
