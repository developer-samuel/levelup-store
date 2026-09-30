<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\Schemas\Tables\Reviews\CreateReviewRatingsTable;

final class Version032_CreateReviewRatingsTable extends AbstractMigration
{
    /**
     * Get description of the migration.
     *
     * @return string
    */
    public function getDescription(): string
    {
        return 'Create review_ratings table';
    }

    /**
     * Apply the migration (create the review_ratings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function up(Schema $schema): void
    {
        CreateReviewRatingsTable::build($schema);
    }

    /**
     * Reverse the migration (drop the review_ratings table).
     *
     * @param Schema $schema
     *
     * @return void
    */
    public function down(Schema $schema): void
    {
        $schema->dropTable('review_ratings');
    }
}
