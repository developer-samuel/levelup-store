<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Catalog;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\IdMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateBrandsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('brands');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addSmallIdColumn($table);
        self::addAdditionalColumn($table);
        self::addTimestamps($table);
    }

    private static function addAdditionalColumn(Table $table): void
    {
        StringMacro::string($table, 'name', 50);
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['name'], 'unique_name_brands');
    }
}
