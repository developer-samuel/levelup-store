<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Passwords\CreatePasswordResetTokenTable;

final class Version027_CreatePasswordResetTokensTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create password_reset_tokens table';
    }

    /**
     * Apply the migration (create the password_reset_tokens table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreatePasswordResetTokenTable::build($schema);
    }

    /**
     * Reverse the migration (drop the password_reset_tokens table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('password_reset_tokens');
    }
}
