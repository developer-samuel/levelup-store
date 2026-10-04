<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Footer\CreateFooterLinksTable;

final class Version010_CreateFooterLinkTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create footer_links table';
    }

    public function up(Schema $schema): void
    {
        CreateFooterLinksTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('footer_links');
    }
}
