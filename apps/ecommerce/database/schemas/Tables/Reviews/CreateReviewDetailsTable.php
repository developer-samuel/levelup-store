<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Reviews;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use App\Core\Domain\Segment\Review\Enum\ReviewDetailType;

use Database\{
    Macros\EnumMacro,
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\IndexMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
};

final class CreateReviewDetailsTable
{
    /**
     * Build the entire schema definition for the 'review_details' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('review_details');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
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
        IdMacro::addIdColumn($table);
        self::addStandardColumn($table);
        self::addAdditionalColumn($table);
        self::addEnumColumn($table);
    }

    /**
     * Add review_id column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumn(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'review_id');
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
        StringMacro::string($table, 'body', 80);
    }

    /**
     * Add enum column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'type',
            ReviewDetailType::cases(),
            ReviewDetailType::POSITIVE->value,
            10,
        );
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
        IndexMacro::add($table, ['review_id'], 'idx_review_details_review_id');
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
        ForeignKeyMacro::addForeignKeys($table, 'reviews', ['review_id'], ['id']);
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
            'chk_body_min_length',
            'body IS NULL OR CHAR_LENGTH(body) >= 1',
        );
    }
}
