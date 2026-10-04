<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Products\Variants;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\IntegerMacro,
    Macros\Integer\SmallIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateProductVariantImagesTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('product_variant_images');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
        self::addCheckConstraints($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumns($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumn(Table $table): void
    {
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    private static function addAdditionalColumns(Table $table): void
    {
        SmallIntegerMacro::smallInteger($table, 'position');
        StringMacro::string($table, 'path');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['path'], 'unique_path_product_variant_images');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['variant_id'], 'idx_product_variant_images_variant_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }

    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_position_positive',
            'position > 0',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_path_min_length',
            'CHAR_LENGTH(path) >= 5',
        );
    }
}
