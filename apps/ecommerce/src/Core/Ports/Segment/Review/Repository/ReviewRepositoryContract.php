<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Repository;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\User\Entity\User
};

/**
 * @phpstan-type ReviewsSummary array{
 *     reviews: Review[],
 *     average: float,
 *     totalRatings: int,
 *     totalFeedbacks: int,
 *     ratingsCount: array<string, int>
 * }
*/
interface ReviewRepositoryContract
{
    /** @return Review[] */
    public function findAll(): array;

    /** @return Review[] */
    public function findAllByVariant(int $variantId, ?int $authUserId = null): array;

    public function existsByVariantAndUser(int $variantId, User $user): bool;
    public function findById(int $id): ?Review;
    public function getLastReviewByVariant(int $variantId): ?Review;

    /** @return ReviewsSummary */
    public function getReviewsAndAverageByVariant(int $variantId): array;

    /**
     * @param list<int> $variantIds
     *
     * @return array<int, float> [variantId => averageRating]
    */
    public function getAverageRatingsByVariantIds(array $variantIds): array;
}
