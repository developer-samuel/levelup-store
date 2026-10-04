<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\Catalog\CreateBrandsTable,
    Schemas\Tables\Catalog\CreateCategoriesTable,
    Schemas\Tables\Catalog\CreateTypesTable,
    Schemas\Tables\Catalog\CreateSubtypesTable
};

final class Version003_CreateCatalogTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create brands, categories, types and subtypes tables';
    }

    public function up(Schema $schema): void
    {
        CreateBrandsTable::build($schema);
        CreateCategoriesTable::build($schema);
        CreateTypesTable::build($schema);
        CreateSubtypesTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('brands');
        $schema->dropTable('categories');
        $schema->dropTable('types');
        $schema->dropTable('subtypes');
    }
}
