<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\User\CreatePasswordResetTokenTable,
    Schemas\Tables\User\CreateRefreshTokensTable,
    Schemas\Tables\User\CreateUserBillingsTable,
    Schemas\Tables\User\CreateUserShippingsTable,
    Schemas\Tables\User\CreateUsersTable,
    Schemas\Tables\User\CreateUserVerificationTokenTable
};

final class Version002_CreateUserTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users, address and authentication token tables';
    }

    public function up(Schema $schema): void
    {
        CreateUsersTable::build($schema);
        CreateUserVerificationTokenTable::build($schema);
        CreateRefreshTokensTable::build($schema);
        CreatePasswordResetTokenTable::build($schema);

        // Address
        CreateUserBillingsTable::build($schema);
        CreateUserShippingsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        // Address
        $schema->dropTable('user_shippings');
        $schema->dropTable('user_billings');

        $schema->dropTable('password_reset_tokens');
        $schema->dropTable('refresh_tokens');
        $schema->dropTable('user_verification_tokens');
        $schema->dropTable('users');
    }
}
