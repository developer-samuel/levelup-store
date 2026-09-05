<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Footer\CreateFooterLinksTable;

final class Version033_CreateFooterLinksTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create footer_links table';
    }

    /**
     * Apply the migration (create the footer_links table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateFooterLinksTable::build($schema);
    }

    /**
     * Reverse the migration (drop the footer_links table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('footer_links');
    }
}
