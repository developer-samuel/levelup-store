<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Banner;

use Doctrine\{
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use App\Core\Domain\Segment\Banner\Entity\Banner;

use App\Core\Ports\Segment\Banner\BannerRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\MaxValue,
    Shared\Traits\IterableQuery
};

/** @extends AbstractRepository<Banner> */
final class BannerRepository extends AbstractRepository implements BannerRepositoryContract
{
    use MaxValue;
    use IterableQuery;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Banner::class,
        );
    }

    protected function getAlias(): string
    {
        return 'b';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'position';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /** @return Banner[] */
    public function findAllActive(): array
    {
        $qb = $this->createActiveQueryBuilder();

        $result = $this->getIterableResult($qb);

        return $this->iteratorCollection(
            $result,
            Banner::class,
        );
    }

    public function findMaxPosition(): int
    {
        return $this->getMaxValue('position');
    }

    private function createActiveQueryBuilder(): QueryBuilder
    {
        return $this->createBaseQueryBuilder()
            ->andWhere('b.isActive = :isActive')
            ->setParameter('isActive', true);
    }
}
