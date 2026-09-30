<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Repository;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewRating,
    Segment\User\Entity\User
};

interface ReviewRatingRepositoryContract
{
    /**
     * @param Review $review
     * @param User $user
     *
     * @return bool
    */
    public function exists(Review $review, User $user): bool;

    /**
     * @param Review $review
     * @param User $user
     *
     * @return ReviewRating|null
    */
    public function findOneByReviewAndUser(Review $review, User $user): ?ReviewRating;

     /**
     * @param int $reviewId
     * @param string $type
     *
     * @return int
    */
    public function countByType(int $reviewId, string $type): int;

    /**
     * @param Review $review
     * @param User $user
     *
     * @return ReviewRating|null
    */
    public function findRatingByUser(Review $review, User $user): ?ReviewRating;
}
