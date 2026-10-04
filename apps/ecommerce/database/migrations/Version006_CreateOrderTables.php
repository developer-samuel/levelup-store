<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\Order\CreateOrderBillingsTable,
    Schemas\Tables\Order\CreateOrderItemsTable,
    Schemas\Tables\Order\CreateOrderPaymentsTable,
    Schemas\Tables\Order\CreateOrderPersonalsTable,
    Schemas\Tables\Order\CreateOrderShippingsTable,
    Schemas\Tables\Order\CreateOrdersTable
};

final class Version006_CreateOrderTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders, items, payments, personal and address tables';
    }

    public function up(Schema $schema): void
    {
        CreateOrdersTable::build($schema);
        CreateOrderItemsTable::build($schema);
        CreateOrderPaymentsTable::build($schema);
        CreateOrderPersonalsTable::build($schema);

        // Address
        CreateOrderBillingsTable::build($schema);
        CreateOrderShippingsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        // Address
        $schema->dropTable('order_shippings');
        $schema->dropTable('order_billings');

        $schema->dropTable('order_personals');
        $schema->dropTable('order_payments');
        $schema->dropTable('order_items');
        $schema->dropTable('orders');
    }
}
