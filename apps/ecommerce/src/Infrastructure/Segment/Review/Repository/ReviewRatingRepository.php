<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Review\Entity\Review,
    Segment\Review\Entity\ReviewRating,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Review\Repository\ReviewRatingRepositoryContract;

use App\Infrastructure\Shared\Traits\SingleResult;

/**
 * @extends ServiceEntityRepository<ReviewRating>
*/
final class ReviewRatingRepository extends ServiceEntityRepository implements ReviewRatingRepositoryContract
{
    use SingleResult;

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            ReviewRating::class,
        );
    }

    /**
     * @param Review $review
     * @param User $user
     *
     * @return bool
    */
    public function exists(Review $review, User $user): bool
    {
        $qb = $this->createQueryBuilder('rr')
            ->select('1')
            ->where('rr.review = :review')
            ->andWhere('rr.user = :user')
            ->setParameter('review', $review)
            ->setParameter('user', $user);

        return $this->getResultOrNull($qb) !== null;
    }

    /**
     * @param Review $review
     * @param User $user
     *
     * @return ReviewRating|null
    */
    public function findOneByReviewAndUser(Review $review, User $user): ?ReviewRating
    {
        return $this->findOneBy(['review' => $review, 'user' => $user]);
    }

    /**
     * @param int $reviewId
     * @param string $type "like" | "dislike"
     *
     * @return int
    */
    public function countByType(int $reviewId, string $type): int
    {
        $qb = $this->createQueryBuilder('rr')
            ->select('COUNT(rr.id)')
            ->where('rr.review = :reviewId')
            ->andWhere('rr.type = :type')
            ->setParameter('reviewId', $reviewId)
            ->setParameter('type', $type);

        return $this->getScalarIntResult($qb);
    }

    /**
     * @param Review $review
     * @param User $user
     *
     * @return ReviewRating|null
    */
    public function findRatingByUser(Review $review, User $user): ?ReviewRating
    {
        $qb = $this->createQueryBuilder('rr')
            ->where('rr.review = :review')
            ->andWhere('rr.user = :user')
            ->setParameter('review', $review)
            ->setParameter('user', $user);

        $rating = $this->getResultOrNull($qb);

        return $rating instanceof ReviewRating ? $rating : null;
    }
}
