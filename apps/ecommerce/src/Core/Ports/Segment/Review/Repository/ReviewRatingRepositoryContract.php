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
    public function exists(Review $review, User $user): bool;
    public function findOneByReviewAndUser(Review $review, User $user): ?ReviewRating;
    public function countByType(int $reviewId, string $type): int;
    public function findRatingByUser(Review $review, User $user): ?ReviewRating;
}
