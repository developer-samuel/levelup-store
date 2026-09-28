<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Security\CreateRefreshTokensTable;

final class Version034_CreateRefreshTokensTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create refresh_tokens table for JWT authentication';
    }

    /**
     * Apply the migration (create the refresh_tokens table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateRefreshTokensTable::build($schema);
    }

    /**
     * Reverse the migration (drop the refresh_tokens table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('refresh_tokens');
    }
}
