<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Banner\CreateBannersTable;

final class Version009_CreateBannerTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create banners table';
    }

    public function up(Schema $schema): void
    {
        CreateBannersTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('banners');
    }
}
