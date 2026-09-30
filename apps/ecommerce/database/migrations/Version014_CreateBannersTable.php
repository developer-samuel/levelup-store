<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Banners\CreateBannersTable;

final class Version014_CreateBannersTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create banners table';
    }

    /**
     * Apply the migration (create the banners table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateBannersTable::build($schema);
    }

    /**
     * Reverse the migration (drop the banners table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('banners');
    }
}
