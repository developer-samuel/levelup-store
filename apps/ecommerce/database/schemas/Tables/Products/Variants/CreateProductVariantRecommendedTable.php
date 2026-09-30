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
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateProductVariantRecommendedTable
{
    /**
     * Build the entire schema definition for the 'product_variant_recommended' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('product_variant_recommended');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndexes($table);
        self::addIndex($table);
        self::addForeignKey($table);
        self::addCheckConstraint($table);
    }

    /**
     * Add columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addColumns(Table $table): void
    {
        IdMacro::addSmallIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumn($table);
        self::addTimestamps($table);
    }

    /**
     * Add variant_id column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumn(Table $table): void
    {
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    /**
     * Add additional column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumn(Table $table): void
    {
        SmallIntegerMacro::smallInteger($table, 'position');
    }

    /**
     * Add timestamp columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    /**
     * Add unique indexes to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addUniqueIndexes(Table $table): void
    {
        UniqueKeyMacro::add($table, ['variant_id'], 'unique_variant_id_product_variant_recommended');
        UniqueKeyMacro::add($table, ['position'], 'unique_position_product_variant_recommended');
    }

    /**
     * Add predefined index to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['variant_id'], 'idx_product_variant_recommended_variant_id');
    }

    /**
     * Add foreign key to the table using ForeignKeyMacro with dynamic parameters.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }

    /**
     * Add check constraint to ensure data validity.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addCheckConstraint(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_position_positive',
            'position > 0',
        );
    }
}
