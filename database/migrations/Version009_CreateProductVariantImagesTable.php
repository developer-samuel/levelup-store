<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Products\Variants\CreateProductVariantImagesTable;

final class Version009_CreateProductVariantImagesTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create product_variant_images table';
    }

    /**
     * Apply the migration (create the product_variant_images table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateProductVariantImagesTable::build($schema);
    }

    /**
     * Reverse the migration (drop the product_variant_images table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('product_variant_images');
    }
}
