<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Orders\CreateOrderBillingsTable;

final class Version022_CreateOrderBillingsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create order_billings table';
    }

    /**
     * Apply the migration (create the order_billings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateOrderBillingsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the order_billings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('order_billings');
    }
}
