<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Catalog;

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
    Macros\TimestampMacro
};

final class CreateSubtypesTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('subtypes');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
        self::addCheckConstraint($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumns($table);
        self::addAdditionalColumn($table);
        self::addTimestamp($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        SmallIntegerMacro::unsignedSmallInteger($table, 'category_id');
        SmallIntegerMacro::unsignedSmallInteger($table, 'type_id');
    }

    private static function addAdditionalColumn(Table $table): void
    {
        StringMacro::string($table, 'name', 100);
    }

    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['category_id'], 'idx_subtypes_category_id');
        IndexMacro::add($table, ['type_id'], 'idx_subtypes_type_id');
    }

    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'categories', ['category_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'types', ['type_id'], ['id']);
    }

    private static function addCheckConstraint(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_name_min_length',
            'CHAR_LENGTH(name) >= 2',
        );
    }
}
