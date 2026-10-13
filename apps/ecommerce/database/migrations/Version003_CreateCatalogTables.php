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
    Schemas\Tables\Catalog\CreateSearchSynonymsTable,
    Schemas\Tables\Catalog\CreateSubtypesTable,
    Schemas\Tables\Catalog\CreateTypesTable
};

final class Version003_CreateCatalogTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create brands, categories, types, subtypes and search_synonyms tables';
    }

    public function up(Schema $schema): void
    {
        CreateBrandsTable::build($schema);
        CreateCategoriesTable::build($schema);
        CreateTypesTable::build($schema);
        CreateSubtypesTable::build($schema);
        CreateSearchSynonymsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('search_synonyms');
        $schema->dropTable('subtypes');
        $schema->dropTable('types');
        $schema->dropTable('categories');
        $schema->dropTable('brands');
    }
}
