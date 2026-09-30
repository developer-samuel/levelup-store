<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Security;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\DateMacro,
    Macros\ForeignKeyMacro,
    Macros\IdMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro
};

final class CreateRefreshTokensTable
{
    /**
     * Build the entire schema definition for the 'refresh_tokens' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('refresh_tokens');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addForeignKeys($table);
        self::addUniqueIndex($table);
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
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
        StringMacro::string($table, 'token', 128);
        DateMacro::datetime($table, 'expires_at');
        DateMacro::datetime($table, 'created_at');
    }

    /**
     * Add foreign keys to the table.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addForeignKeys(Table $table): void
    {
        ForeignKeyMacro::addForeignKeys(
            $table,
            'users',
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE'],
        );
    }

    /**
     * Add unique index on token column.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addUniqueIndex(Table $table): void
    {
        $table->addUniqueIndex(['token'], 'uniq_refresh_token');
    }
}
