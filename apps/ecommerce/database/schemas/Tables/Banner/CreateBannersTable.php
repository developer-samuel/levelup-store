<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Banner;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\BooleanMacro,
    Macros\EnumMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\Integer\SmallIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

use App\Core\Domain\Segment\Banner\Enum\BannerType;

final class CreateBannersTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('banners');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndexes($table);
        self::addCheckConstraints($table);
    }

    public static function addColumns(Table $table): void
    {
        IdMacro::addSmallIdColumn($table);
        self::addAdditionalColumns($table);
        self::addEnumColumn($table);
        self::addBooleanColumn($table);
        self::addTimestamps($table);
    }

    private static function addAdditionalColumns(Table $table): void
    {
        SmallIntegerMacro::unsignedSmallInteger($table, 'position');
        StringMacro::string($table, 'name', 100);
        StringMacro::string($table, 'image', 255, ['notnull' => false]);
        StringMacro::string($table, 'url', 255, ['notnull' => false]);
    }

    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table,
            'type',
            BannerType::cases(),
            BannerType::BACKGROUND->value,
            50,
        );
    }

    private static function addBooleanColumn(Table $table): void
    {
        BooleanMacro::add($table, 'is_active', true);
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table);
    }

    private static function addUniqueIndexes(Table $table): void
    {
        UniqueKeyMacro::add($table, ['position'], 'unique_position_banners');
        UniqueKeyMacro::add($table, ['name'], 'unique_name_banners');
        UniqueKeyMacro::add($table, ['image'], 'unique_image_banners');
        UniqueKeyMacro::add($table, ['url'], 'unique_url_banners');
    }

    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_name_min_length',
            'CHAR_LENGTH(name) >= 2',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_image_min_length',
            'image IS NULL OR CHAR_LENGTH(image) >= 5',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_url_min_length',
            'url IS NULL OR CHAR_LENGTH(url) >= 5',
        );
    }
}
