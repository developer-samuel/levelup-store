<?php

declare(strict_types=1);

namespace Database\Seeds\Factories\Review;

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewRating,
    Segment\Review\Enum\ReviewRatingType,
    Segment\User\Entity\User
};

trait ReviewRatingFactory
{
    /**
     * @param Review $review
     * @param User $user
     *
     * @return ReviewRating
    */
    private function createRandomRating(Review $review, User $user): ReviewRating
    {
        return (new ReviewRating())
            ->setReview($review)
            ->setUser($user)
            ->setType($this->randomRatingType());
    }

    /**
     * @return ReviewRatingType
    */
    private function randomRatingType(): ReviewRatingType
    {
        return rand(0, 1) === 1 ? ReviewRatingType::LIKE : ReviewRatingType::DISLIKE;
    }
}
