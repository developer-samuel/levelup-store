<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\User;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\DateMacro,
    Macros\ForeignKeyMacro,
    Macros\CheckConstraintMacro,
    Macros\IdMacro,
    Macros\IndexMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro,
    Macros\TimestampMacro,
    Macros\UniqueKeyMacro
};

final class CreatePasswordResetTokenTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('password_reset_tokens');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndexes($table);
        self::addIndexes($table);
        self::addForeignKey($table);
        self::addCheckConstraints($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addIdColumn($table);
        self::addStandardColumns($table);
        self::addAdditionalColumn($table);
        self::addDateTimeColumn($table);
        self::addTimestamp($table);
    }

    private static function addStandardColumns(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
    }

    private static function addAdditionalColumn(Table $table): void
    {
        StringMacro::string($table, 'token', 128);
    }

    private static function addDateTimeColumn(Table $table): void
    {
        DateMacro::datetime($table, 'expires_at');
    }

    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
    }

    private static function addUniqueIndexes(Table $table): void
    {
        UniqueKeyMacro::add($table, ['user_id'], 'unique_user_id_password_reset_tokens');
        UniqueKeyMacro::add($table, ['token'], 'unique_token_password_reset_tokens');
    }

    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['user_id'], 'idx_password_reset_tokens_user_id');
    }

    private static function addForeignKey(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
    }

    private static function addCheckConstraints(Table $table): void
    {
        CheckConstraintMacro::add(
            $table,
            'chk_token_length',
            'CHAR_LENGTH(token) = 128',
        );

        CheckConstraintMacro::add(
            $table,
            'chk_expires_at_future',
            'expires_at > NOW()',
        );
    }
}
