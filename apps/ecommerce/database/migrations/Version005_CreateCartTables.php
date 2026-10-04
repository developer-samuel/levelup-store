<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\Cart\CreateCartItemsTable,
    Schemas\Tables\Cart\CreateCartsTable
};

final class Version005_CreateCartTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create carts and cart items table';
    }

    public function up(Schema $schema): void
    {
        CreateCartsTable::build($schema);
        CreateCartItemsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('cart_items');
        $schema->dropTable('carts');
    }
}
