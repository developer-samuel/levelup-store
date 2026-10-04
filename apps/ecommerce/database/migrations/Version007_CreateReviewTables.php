<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\{
    DBAL\Schema\Schema,
    Migrations\AbstractMigration
};

use Database\{
    Schemas\Tables\Review\CreateReviewDetailsTable,
    Schemas\Tables\Review\CreateReviewRatingsTable,
    Schemas\Tables\Review\CreateReviewsTable
};

final class Version007_CreateReviewTables extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create reviews, details and ratings tables';
    }

    public function up(Schema $schema): void
    {
        CreateReviewsTable::build($schema);
        CreateReviewDetailsTable::build($schema);
        CreateReviewRatingsTable::build($schema);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('review_ratings');
        $schema->dropTable('review_details');
        $schema->dropTable('reviews');
    }
}
