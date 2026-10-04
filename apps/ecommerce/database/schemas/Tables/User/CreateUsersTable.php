<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\User;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\BooleanMacro,
    Macros\DateMacro,
    Macros\EnumMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

use App\Core\Domain\Segment\User\Enum\UserRole;

final class CreateUsersTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('users');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addCheckConstraints($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        self::addAdditionalColumns($table);
        self::addEnumColumn($table);
        self::addBooleanColumn($table);
        self::addDateTimeColumn($table);
        self::addTimestamps($table);
        self::addSoftDeleteColumn($table);
    }

    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'email');
        StringMacro::string($table, 'first_name', 100);
        StringMacro::string($table, 'last_name', 100);
        StringMacro::string($table, 'password', 100);
    }

    private static function addEnumColumn(Table $table): void
    {
        EnumMacro::add(
            $table, 'role',
            UserRole::cases(),
            UserRole::USER->value,
            10,
        );
    }

    private static function addBooleanColumn(Table $table): void
    {
        BooleanMacro::add($table, 'use_shipping');
    }

    private static function addDateTimeColumn(Table $table): void
    {
        DateMacro::datetime($table, 'email_verified_at');
    }

    private static function addTimestamps(Table $table): void
    {
        TimestampMacro::created($table);
        TimestampMacro::updated($table, false);
    }

    private static function addSoftDeleteColumn(Table $table): void
    {
        DateMacro::datetime($table, 'deleted_at');
    }

    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['email'], 'unique_email_users');
    }

    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_email_min_length',
            'CHAR_LENGTH(email) >= 5',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_first_name_min_length',
            'CHAR_LENGTH(first_name) >= 2',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_last_name_min_length',
            'CHAR_LENGTH(last_name) >= 2',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_password_min_length',
            'CHAR_LENGTH(password) >= 8',
        );
    }
}
