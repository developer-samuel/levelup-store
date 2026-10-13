<?php

declare(strict_types=1);

namespace Database\Schemas\Tables\Messenger;

use Doctrine\{
    DBAL\Schema\Schema,
    DBAL\Schema\Table
};

use Database\{
    Macros\DateMacro,
    Macros\IdMacro,
    Macros\PrimaryKeyMacro,
    Macros\StringMacro
};

final class CreateMessengerMessagesTable
{
    public static function build(Schema $schema): void
    {
        $table = $schema->createTable('messenger_messages');

        self::addColumns($table);
        PrimaryKeyMacro::add($table);
        self::addIndexes($table);
    }

    private static function addColumns(Table $table): void
    {
        IdMacro::addBigIdColumn($table);
        StringMacro::text($table, 'body');
        StringMacro::text($table, 'headers');
        StringMacro::string($table, 'queue_name', 190);
        DateMacro::datetimetz($table, 'created_at', ['notnull' => true]);
        DateMacro::datetimetz($table, 'available_at', ['notnull' => true]);
        DateMacro::datetimetz($table, 'delivered_at');
    }

    private static function addIndexes(Table $table): void
    {
        $table->addIndex(['queue_name'], 'idx_messenger_messages_queue_name');
        $table->addIndex(['available_at'], 'idx_messenger_messages_available_at');
        $table->addIndex(['delivered_at'], 'idx_messenger_messages_delivered_at');
    }
}
