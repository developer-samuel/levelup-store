<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Cart;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\DateMacro,
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateCartsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('carts');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addStandardColumn($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumn(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
        DateMacro::datetime($table, 'reminder_sent_at');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['user_id'], 'unique_user_id_carts');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['user_id'], 'idx_carts_user_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
    }
}
