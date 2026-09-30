<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Review\Service\Query;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\User\Entity\User
};

interface ReviewRatingQueryContract
{
    /**
     * @param Review $review
     * @param User|null $user
     *
     * @return array<string, int|string>
    */
    public function getReviewFeedbackStats(Review $review, ?User $user): array;
}
