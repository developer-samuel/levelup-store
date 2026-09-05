<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Orders\CreateOrderPaymentsTable;

final class Version020_CreateOrderPaymentsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create order_payments table';
    }

    /**
     * Apply the migration (create the order_payments table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateOrderPaymentsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the order_payments table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('order_payments');
    }
}
