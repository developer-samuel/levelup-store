<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Audit;

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

final class CreateAuditLogsTable
{
    /**
     * Build the entire schema definition for the 'audit_logs' table.
     *
     * @param Schema $schema
     *
     * @return void
    */
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('audit_logs');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addForeignKeys($table);
        self::addIndexes($table);
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
        BigIntegerMacro::unsignedBigInteger($table, 'user_id', null, ['unsigned' => true, 'notnull' => false]);
        StringMacro::string($table, 'action', 64);
        StringMacro::string($table, 'entity', 64);
        BigIntegerMacro::unsignedBigInteger($table, 'entity_id');
        StringMacro::text($table, 'metadata', ['notnull' => false, 'default' => null]);
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
            ['onUpdate' => 'CASCADE'],
        );
    }

    /**
     * Add indexes for common query patterns.
     *
     * @param Table $table
     *
     * @return void
    */
    private static function addIndexes(Table $table): void
    {
        $table->addIndex(['user_id'], 'idx_audit_logs_user_id');
        $table->addIndex(['action'], 'idx_audit_logs_action');
        $table->addIndex(['created_at'], 'idx_audit_logs_created_at');
    }
}
