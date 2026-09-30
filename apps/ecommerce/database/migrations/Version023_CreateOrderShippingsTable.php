<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Orders\CreateOrderShippingsTable;

final class Version023_CreateOrderShippingsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create order_shippings table';
    }

    /**
     * Apply the migration (create the order_shippings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateOrderShippingsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the order_shippings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('order_shippings');
    }
}
