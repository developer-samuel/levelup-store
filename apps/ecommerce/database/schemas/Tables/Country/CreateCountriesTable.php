<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Country;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\UniqueKeyMacro
};

final class CreateCountriesTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('countries');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndexes($table);
        self::addCheckConstraint($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addAdditionalColumn($table);
    }

    private static function addAdditionalColumn(Table $table): void
    {
        StringMacro::string($table, 'code', 2);
        StringMacro::string($table, 'name', 100);
    }

    private static function addUniqueIndexes(Table $table): void
    {
        UniqueKeyMacro::add($table, ['code'], 'unique_code_countries');
        UniqueKeyMacro::add($table, ['name'], 'unique_name_countries');
    }

    private static function addCheckConstraint(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_code_length',
            'CHAR_LENGTH(code) = 2',
        );
    }
}
