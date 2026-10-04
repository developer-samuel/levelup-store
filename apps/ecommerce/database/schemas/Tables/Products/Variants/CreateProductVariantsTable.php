<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Products\Variants;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\EnumMacro,
    Macros\DecimalMacro,
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\IntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

use App\Core\Domain\Segment\Product\Enum\Variant\ProductVariantStatus;

final class CreateProductVariantsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('product_variants');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndexes($table);
        self::addIndex($table);
        self::addForeignKey($table);
        self::addCheckConstraints($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumns($table);
        self::addEnumColumn($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumn(Table $table): void
    {
        IntegerMacro::unsignedInteger($table, 'product_id');
    }

    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'sku', 100);
        StringMacro::string($table, 'name');
        DecimalMacro::add($table, 'price');
        StringMacro::text($table, 'description', ['notnull' => false]);
        StringMacro::string($table, 'url');
    }

    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'status',
            ProductVariantStatus::cases(),
            ProductVariantStatus::AVAILABLE->value,
            10,
        );
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndexes(Table $table): void
    {
        UniqueKeyMacro::add($table, ['sku'], 'unique_sku_product_variants');
        UniqueKeyMacro::add($table, ['name'], 'unique_name_product_variants');
        UniqueKeyMacro::add($table, ['url'], 'unique_url_product_variants');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['product_id'], 'idx_product_variants_product_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'products', ['product_id'], ['id']);
    }

    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_sku_min_length',
            'CHAR_LENGTH(sku) >= 6',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_name_min_length',
            'CHAR_LENGTH(name) >= 3',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_price_positive',
            'price > 0',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_description_min_length',
            'description IS NULL OR CHAR_LENGTH(description) >= 20',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_url_min_length',
            'CHAR_LENGTH(url) >= 6',
        );
    }
}
