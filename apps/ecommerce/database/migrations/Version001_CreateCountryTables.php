<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Country\CreateCountriesTable;

final class Version001_CreateCountryTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create countries table';
    }

    public function up(Schema $schema): void
    {
        CreateCountriesTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('countries');
    }
}
