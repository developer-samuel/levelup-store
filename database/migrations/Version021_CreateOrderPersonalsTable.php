<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Orders\CreateOrderPersonalsTable;

final class Version021_CreateOrderPersonalsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create order_personals table';
    }

    /**
     * Apply the migration (create the order_personals table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateOrderPersonalsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the order_personals table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('order_personals');
    }
}
