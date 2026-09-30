<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Products\Variants\CreateProductVariantDescriptionsTable;

final class Version010_CreateProductVariantDescriptionsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create product_variant_descriptions table';
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
        CreateProductVariantDescriptionsTable::build($schema);
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
        $schema->dropTable('product_variant_descriptions');
    }
}
