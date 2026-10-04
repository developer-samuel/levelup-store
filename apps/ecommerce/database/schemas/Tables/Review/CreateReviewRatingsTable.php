<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Review;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use App\Core\Domain\Segment\Review\Enum\ReviewRatingType;

use Database\{
    Macros\EnumMacro,
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreateReviewRatingsTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('review_ratings');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndex($table);
        self::addForeignKey($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumns($table);
        self::addEnumColumn($table);
        self::addTimestamp($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'review_id');
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
    }

    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'type',
            ReviewRatingType::cases(),
            ReviewRatingType::LIKE->value,
            10,
        );
    }

    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['review_id', 'user_id'], 'unique_review_user');
    }

    private static function addIndex(Table $table): void
    {
        IndexMacro::add($table, ['review_id'], 'idx_review_ratings_review_id');
        IndexMacro::add($table, ['user_id'], 'idx_review_ratings_user_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'reviews', ['review_id'], ['id']);
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
    }
}
