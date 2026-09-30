<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Products\CreateProductSubtypesTable;

final class Version008_CreateProductSubtypesTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create product_subtypes table';
    }

    /**
     * Apply the migration (create the product_subtypes table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateProductSubtypesTable::build($schema);
    }

    /**
     * Reverse the migration (drop the product_subtypes table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('product_subtypes');
    }
}
