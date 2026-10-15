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
    Macros\TimestampMacro
};

final class CreateSearchSynonymsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('search_synonyms');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addSmallIdColumn($table);
        self::addSynonymsColumn($table);
        self::addTimestamps($table);
    }

    private static function addSynonymsColumn(Table $table): void
    {
        StringMacro::text($table, 'synonyms');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }
}
