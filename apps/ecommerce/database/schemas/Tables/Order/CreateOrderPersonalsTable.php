<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Order;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\UniqueKeyMacro
};

final class CreateOrderPersonalsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('order_personals');

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
        self::addAdditionalColumns($table);
    }

    private static function addStandardColumn(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'order_id');
    }

    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'email', 255);
        StringMacro::string($table, 'first_name', 100);
        StringMacro::string($table, 'last_name', 100);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['order_id'], 'unique_order_id_order_personals');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['order_id'], 'idx_order_personals_order_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'orders', ['order_id'], ['id']);
    }
}
