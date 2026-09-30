<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Reviews\CreateReviewDetailsTable;

final class Version031_CreateReviewDetailsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create review_details table';
    }

    /**
     * Apply the migration (create the review_details table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateReviewDetailsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the review_details table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('review_details');
    }
}
