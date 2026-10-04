<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Wishlist\CreateWishlistsTable;

final class Version008_CreateWishlistTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create wishlists table';
    }

    public function up(Schema $schema): void
    {
        CreateWishlistsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('wishlists');
    }
}
