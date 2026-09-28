<?php

declare(strict_types=1);

namespace App\Core\Application\Segment\Review\Service\Command;

use Kit\Assertion\Domain\Review\ReviewAssertion;

use App\Core\Domain\{
    Shared\Exception\NotFoundException,
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewRating,
    Segment\Review\Enum\ReviewRatingType,
    Segment\User\Entity\User
};

use App\Core\Ports\{
    Segment\Review\Repository\ReviewRatingRepositoryContract,
    Segment\Review\Repository\ReviewRepositoryContract,
    Segment\Review\Service\Command\ReviewRatingCommandContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class ReviewRatingCommandService implements ReviewRatingCommandContract
{
    /**
     * @param EntityPersistenceContract $entityPersistence
     * @param ReviewRepositoryContract $reviewRepository
     * @param ReviewRatingRepositoryContract $reviewRatingRepository
    */
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private ReviewRepositoryContract $reviewRepository,
        private ReviewRatingRepositoryContract $reviewRatingRepository,
    ) {}

    /**
     * @param int $id
     * @param User $user
     * @param string|null $type
     *
     * @return bool
     *
     * @throws NotFoundException
    */
    public function toggle(int $id, User $user, ?string $type): bool
    {
        $review = $this->reviewRepository->findById($id);
        ReviewAssertion::assertExists($review);

        $this->remove($review, $user);

        if ($type !== null) {
            $this->add($review, $user, $type);

            return true;
        }

        return false;
    }

    /**
     * @param Review $review
     * @param User $user
     * @param string $type
     *
     * @return void
    */
    private function add(Review $review, User $user, string $type): void
    {
        if ($type === '') {
            return;
        }

        $ratingType = ReviewRatingType::from($type);

        if ($this->reviewRatingRepository->exists($review, $user)) {
            return;
        }

        $reviewRating = $this->createReviewRating($review, $user, $ratingType);

        $this->entityPersistence->persist($reviewRating, true);
    }

    /**
     * @param Review $review
     * @param User $user
     *
     * @return void
    */
    private function remove(Review $review, User $user): void
    {
        $item = $this->reviewRatingRepository->findOneByReviewAndUser($review, $user);

        if ($item !== null) {
            $this->entityPersistence->remove($item, true);
        }
    }

    /**
     * @param Review $review
     * @param User $user
     * @param ReviewRatingType $ratingType
     *
     * @return ReviewRating
    */
    private function createReviewRating(Review $review, User $user, ReviewRatingType $ratingType): ReviewRating
    {
        return (new ReviewRating())
            ->setReview($review)
            ->setUser($user)
            ->setType($ratingType);
    }
}
