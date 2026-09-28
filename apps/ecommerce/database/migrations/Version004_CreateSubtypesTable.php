<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Subtypes\CreateSubtypesTable;

final class Version004_CreateSubtypesTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create subtypes table';
    }

    /**
     * Apply the migration (create the subtypes table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateSubtypesTable::build($schema);
    }

    /**
     * Reverse the migration (drop the subtypes table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('subtypes');
    }
}