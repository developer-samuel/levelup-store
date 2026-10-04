<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\Products\CreateProductsTable,
    Schemas\Tables\Products\CreateProductSubtypesTable,
    Schemas\Tables\Products\Variants\CreateProductVariantDescriptionsTable,
    Schemas\Tables\Products\Variants\CreateProductVariantDiscountsTable,
    Schemas\Tables\Products\Variants\CreateProductVariantEansTable,
    Schemas\Tables\Products\Variants\CreateProductVariantImagesTable,
    Schemas\Tables\Products\Variants\CreateProductVariantRecommendedTable,
    Schemas\Tables\Products\Variants\CreateProductVariantsTable,
    Schemas\Tables\Products\Variants\CreateProductVariantStocksTable
};

final class Version004_CreateProductTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create product and product variant tables';
    }

    public function up(Schema $schema): void
    {
        CreateProductsTable::build($schema);
        CreateProductSubtypesTable::build($schema);

        // Variants
        CreateProductVariantsTable::build($schema);
        CreateProductVariantDiscountsTable::build($schema);
        CreateProductVariantRecommendedTable::build($schema);
        CreateProductVariantImagesTable::build($schema);
        CreateProductVariantStocksTable::build($schema);
        CreateProductVariantEansTable::build($schema);
        CreateProductVariantDescriptionsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        // Variants
        $schema->dropTable('product_variant_descriptions');
        $schema->dropTable('product_variant_eans');
        $schema->dropTable('product_variant_stocks');
        $schema->dropTable('product_variant_images');
        $schema->dropTable('product_variant_recommended');
        $schema->dropTable('product_variant_discounts');
        $schema->dropTable('product_variants');

        $schema->dropTable('product_subtypes');
        $schema->dropTable('products');
    }
}
