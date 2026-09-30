<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Users\CreateUserBillingsTable;

final class Version024_CreateUserBillingsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create user_billings table';
    }

    /**
     * Apply the migration (create the user_billings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateUserBillingsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the user_billings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('user_billings');
    }
}
