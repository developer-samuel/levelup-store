<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Review\Repository;

use Doctrine\{
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use App\Core\Domain\{
    Segment\Review\Aggregate\ReviewStatistics,
    Segment\Review\Entity\Review,
    Segment\Review\Enum\ReviewType,
    Segment\User\Entity\User
};

use App\Core\Ports\Segment\Review\Repository\ReviewRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\IterableQuery,
    Shared\Traits\SingleResult
};

/**
 * @phpstan-import-type ReviewsSummary from ReviewRepositoryContract
 *
 * @extends AbstractRepository<Review>
*/
class ReviewRepository extends AbstractRepository implements ReviewRepositoryContract
{
    use SingleResult;
    use IterableQuery;

    private const VARIANT_CONDITION = 'r.variant = :variantId';

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Review::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'r';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'createdAt';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::DESC;
    }

    /**
     * @param int $variantId
     * @param int|null $authUserId
     *
     * @return Review[]
    */
    public function findAllByVariant(int $variantId, ?int $authUserId = null): array
    {
        $qb = $this->createBaseQueryForVariantAndType($variantId);

        if ($authUserId !== null) {
            $this->applyAuthUserOrdering($qb, $authUserId);
        }

        if ($authUserId == null) {
            $qb->orderBy('r.createdAt', SortDirection::DESC->value);
        }

        $results = $this->getIterableResult($qb);

        return $this->iteratorCollection(
            $results,
            Review::class,
        );
    }

    /**
     * @param int $variantId
     * @param User $user
     *
     * @return bool
    */
    public function existsByVariantAndUser(int $variantId, User $user): bool
    {
        $qb = $this->createExistsQueryForVariantAndUser($variantId, $user);

        return $this->getResultOrNull($qb) !== null;
    }

    /**
     * @param int $id
     *
     * @return Review|null
    */
    public function findById(int $id): ?Review
    {
        return $this->find($id);
    }

    /**
     * @param int $variantId
     *
     * @return Review|null
    */
    public function getLastReviewByVariant(int $variantId): ?Review
    {
        $qb = $this->createBaseQueryForVariantAndType($variantId)
            ->orderBy('r.createdAt', SortDirection::DESC->value);

        $review = $this->getResultOrNull($qb);

        return $review instanceof Review ? $review : null;
    }

    /**
     * @param int $variantId
     *
     * @return ReviewsSummary
    */
    public function getReviewsAndAverageByVariant(int $variantId): array
    {
        $reviews = $this->fetchReviewsByVariant($variantId);

        return $this->calculateSummaryWithTypedKeys($reviews);
    }

    /**
     * @param list<int> $variantIds
     *
     * @return array<int, float> [variantId => averageRating]
    */
    public function getAverageRatingsByVariantIds(array $variantIds): array
    {
        if (empty($variantIds)) {
            return [];
        }

        $rows = $this->createQueryBuilder('r')
            ->select('IDENTITY(r.variant) AS variantId, AVG(r.value) AS avgRating')
            ->where('r.variant IN (:variantIds)')
            ->setParameter('variantIds', $variantIds)
            ->groupBy('r.variant')
            ->getQuery()
            ->getScalarResult();

        $ratings = array_fill_keys($variantIds, 0.0);

        foreach ($rows as $row) {
            /** @var array{variantId: string, avgRating: string|null} $row */
            $ratings[(int) $row['variantId']] = round((float) $row['avgRating'], 2);
        }

        return $ratings;
    }

    /**
     * @param int $variantId
     *
     * @return QueryBuilder
    */
    private function createBaseQueryForVariantAndType(int $variantId): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where(self::VARIANT_CONDITION)
            ->andWhere('r.type = :type')
            ->setParameter('variantId', $variantId)
            ->setParameter('type', ReviewType::FEEDBACK->value);
    }

    /**
     * @param QueryBuilder $qb
     * @param int $authUserId
     *
     * @return void
    */
    private function applyAuthUserOrdering(QueryBuilder $qb, int $authUserId): void
    {
        $qb->addSelect("(CASE WHEN r.user = :authUserId THEN 0 ELSE 1 END) AS HIDDEN user_order")
            ->setParameter('authUserId', $authUserId)
            ->orderBy('user_order', SortDirection::ASC->value)
            ->addOrderBy('r.createdAt', SortDirection::DESC->value);
    }

    /**
     * @param int $variantId
     * @param User $user
     *
     * @return QueryBuilder
    */
    private function createExistsQueryForVariantAndUser(int $variantId, User $user): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->select('1')
            ->where(self::VARIANT_CONDITION)
            ->andWhere('r.user = :user')
            ->setParameter('variantId', $variantId)
            ->setParameter('user', $user)
            ->setMaxResults(1);
    }

    /**
     * @param int $variantId
     *
     * @return Review[]
    */
    private function fetchReviewsByVariant(int $variantId): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where(self::VARIANT_CONDITION)
            ->setParameter('variantId', $variantId);

        $results = $this->getIterableResult($qb);

        return $this->iteratorCollection(
            $results,
            Review::class,
        );
    }

    /**
     * @param Review[] $reviews
     *
     * @return ReviewsSummary
    */
    private function calculateSummaryWithTypedKeys(array $reviews): array
    {
        $summary = ReviewStatistics::calculateSummary($reviews);

        return [
            'reviews'        => $reviews,
            'average'        => (float) $summary['average'],
            'totalRatings'   => (int) $summary['totalRatings'],
            'totalFeedbacks' => (int) $summary['totalFeedbacks'],
            'ratingsCount'   => $this->castRatingsCountToStringKeys($summary['ratingsCount']),
        ];
    }

    /**
     * @param array<int|string, int> $ratingsCount
     *
     * @return array<string, int>
    */
    private function castRatingsCountToStringKeys(array $ratingsCount): array
    {
        $typed = [];
        foreach ($ratingsCount as $key => $value) {
            $typed[(string) $key] = (int) $value;
        }

        return $typed;
    }
}
