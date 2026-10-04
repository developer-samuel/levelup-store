<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Products\Variants;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\DecimalMacro,
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\IntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateProductVariantDiscountsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('product_variant_discounts');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
        self::addCheckConstraint($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumn($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumn(Table $table): void
    {
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    private static function addAdditionalColumn(Table $table): void
    {
        DecimalMacro::add($table, 'price');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['variant_id'], 'unique_variant_id_product_variant_discounts');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['variant_id'], 'idx_product_variant_discounts_variant_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }

    private static function addCheckConstraint(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_price_positive',
            'price > 0',
        );
    }
}
