<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Products\Variants;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\EnumMacro,
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\IntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

use App\Core\Domain\Segment\Product\Enum\ProductStockStatus;

final class CreateProductVariantStocksTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('product_variant_stocks');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
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
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    private static function addAdditionalColumns(Table $table): void
    {
        IntegerMacro::integer($table, 'quantity_available');
        IntegerMacro::integer($table, 'quantity_reserved');
        IntegerMacro::integer($table, 'quantity_refunded');
    }

    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'status',
            ProductStockStatus::cases(),
            ProductStockStatus::IN_STOCK->value,
            20,
        );
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['variant_id'], 'unique_variant_id_product_variant_stocks');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['variant_id'], 'idx_product_variant_stocks_variant_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }
}
