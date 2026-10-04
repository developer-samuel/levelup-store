<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Review;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use App\Core\Domain\Segment\Review\Enum\ReviewType;

use Database\{
    Macros\EnumMacro,
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\Integer\IntegerMacro,
    Macros\Integer\SmallIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
};

final class CreateReviewsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('reviews');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
        self::addForeignKeys($table);
        self::addCheckConstraint($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addStandardColumns($table);
        self::addAdditionalColumns($table);
        self::addEnumColumn($table);
        self::addTimestamps($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
        IntegerMacro::unsignedInteger($table, 'variant_id');
    }

    private static function addAdditionalColumns(Table $table): void
    {
        SmallIntegerMacro::smallInteger($table, 'value', 1, [
            'comment' => 'Review value must be between 1 and 5',
        ]);
        StringMacro::string($table, 'body', 250, ['notnull' => false]);
    }

    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'type',
            ReviewType::cases(),
            ReviewType::RATING->value,
            10,
        );
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['user_id'], 'idx_reviews_user_id');
        IndexMacro::add($table, ['variant_id'], 'idx_reviews_variant_id');
    }

    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'product_variants', ['variant_id'], ['id']);
    }

    private static function addCheckConstraint(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_review_value_allowed',
            'value IN (1, 2, 3, 4, 5)',
        );
    }
}
