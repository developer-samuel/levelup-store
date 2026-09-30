<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Products\Variants\CreateProductVariantsTable;

final class Version007_CreateProductVariantsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create product_variants table';
    }

    /**
     * Apply the migration (create the product_variants table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateProductVariantsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the product_variants table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('product_variants');
    }
}
