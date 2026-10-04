<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Audit\CreateAuditLogsTable;

final class Version011_CreateAuditLogTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create audit log tables';
    }

    public function up(Schema $schema): void
    {
        CreateAuditLogsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('audit_logs');
    }
}
