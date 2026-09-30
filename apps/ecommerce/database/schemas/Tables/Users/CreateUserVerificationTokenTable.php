<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Users;

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

final class CreateUserVerificationTokenTable
{
    /**
     * Build the entire schema definition for the 'user_verification_tokens' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('user_verification_tokens');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addUniqueIndex($table);
        self::addIndexes($table);
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
        self::addDateTimeColumn($table);
        self::addTimestamp($table);
    }

    /**
     * Add user_id column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addStandardColumn(Table $table): void
    {
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
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
        StringMacro::string($table, 'token', 128);
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
        DateMacro::datetime($table, 'expires_at');
    }

    /**
     * Add timestamp column to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addTimestamp(Table $table): void
    {
        TimestampMacro::created($table);
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
        UniqueKeyMacro::add($table, ['token'], 'unique_token_user_verification_tokens');
    }

    /**
     * Add predefined indexes to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addIndexes(Table $table): void
    {
        IndexMacro::add($table, ['user_id'], 'idx_user_verification_tokens_user_id');
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
        ForeignKeyMacro::addForeignKeys($table, 'users', ['user_id'], ['id']);
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
            'chk_token_length',
            'CHAR_LENGTH(token) = 128',
        );
    }
}
