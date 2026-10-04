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
    Macros\IdMacro,
    Macros\Integer\BigIntegerMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro
};

final class CreateRefreshTokensTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('refresh_tokens');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addForeignKeys($table);
        self::addUniqueIndex($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        BigIntegerMacro::unsignedBigInteger($table, 'user_id');
        StringMacro::string($table, 'token', 128);
        DateMacro::datetime($table, 'expires_at');
        DateMacro::datetime($table, 'created_at');
    }

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

    private static function addUniqueIndex(Table $table): void
    {
        $table->addUniqueIndex(['token'], 'uniq_refresh_token');
    }
}
