<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Database\Schemas\Tables\Messenger\CreateMessengerMessagesTable;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

final class Version012_CreateMessengerTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create messenger_messages table for outbox pattern (email/notifier guaranteed delivery)';
    }

    public function up(Schema $schema): void
    {
        CreateMessengerMessagesTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('messenger_messages');
    }
}
