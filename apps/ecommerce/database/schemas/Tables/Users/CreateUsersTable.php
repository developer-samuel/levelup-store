<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Users;

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
    /**
     * Build the entire schema definition for the 'users' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('users');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addCheckConstraints($table);
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
        IdMacro::addBigIdColumn($table);
        self::addAdditionalColumns($table);
        self::addEnumColumn($table);
        self::addBooleanColumn($table);
        self::addDateTimeColumn($table);
        self::addTimestamps($table);
        self::addSoftDeleteColumn($table);
    }

    /**
     * Add additional columns to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addAdditionalColumns(Table $table): void
    {
        StringMacro::string($table, 'email');
        StringMacro::string($table, 'first_name', 100);
        StringMacro::string($table, 'last_name', 100);
        StringMacro::string($table, 'password', 100);
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
            $table, 'role',
            UserRole::cases(),
            UserRole::USER->value,
            10,
        );
    }

    /**
     * Add boolean column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addBooleanColumn(Table $table): void
    {
        BooleanMacro::add($table, 'use_shipping');
    }

    /**
     * Add date time column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addDateTimeColumn(Table $table): void
    {
        DateMacro::datetime($table, 'email_verified_at');
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
        TimestampMacro::updated($table, false);
    }

    /**
     * Add soft delete column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addSoftDeleteColumn(Table $table): void
    {
        DateMacro::datetime($table, 'deleted_at');
    }

    /**
     * Add unique index to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addUniqueIndex(Table $table): void
    {
        UniqueKeyMacro::add($table, ['email'], 'unique_email_users');
    }

    /**
     * Add check constraints to ensure data validity.
     *
     * @param Table $table
     *
     * @return void
    */
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
