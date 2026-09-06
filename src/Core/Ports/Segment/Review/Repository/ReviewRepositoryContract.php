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
    /**
     * @return Review[]
    */
    public function findAll(): array;

    /**
     * @param int $variantId
     * @param int|null $authUserId
     *
     * @return Review[]
    */
    public function findAllByVariant(int $variantId, ?int $authUserId = null): array;

    /**
     * @param int $variantId
     * @param User $user
     *
     * @return bool
    */
    public function existsByVariantAndUser(int $variantId, User $user): bool;

    /**
     * @param int $id
     *
     * @return Review|null
    */
    public function findById(int $id): ?Review;

    /**
     * @param int $variantId
     *
     * @return Review|null
    */
    public function getLastReviewByVariant(int $variantId): ?Review;

    /**
     * @param int $variantId
     *
     * @return ReviewsSummary
    */
    public function getReviewsAndAverageByVariant(int $variantId): array;

    /**
     * @param list<int> $variantIds
     *
     * @return array<int, float> [variantId => averageRating]
    */
    public function getAverageRatingsByVariantIds(array $variantIds): array;
}
