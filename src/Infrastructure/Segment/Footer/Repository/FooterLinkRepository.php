<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Footer\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\{
    Segment\Footer\Entity\FooterLink,
    Segment\Footer\Enum\FooterLinkGroup
};

use App\Core\Ports\Segment\Footer\Repository\FooterLinkRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/**
 * @extends AbstractRepository<FooterLink>
*/
final class FooterLinkRepository extends AbstractRepository implements FooterLinkRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            FooterLink::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'fl';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'position';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /**
     * @return FooterLink[]
    */
    public function findAllOrderedByGroup(): array
    {
        /** @var FooterLink[] $results */
        $results = $this->createQueryBuilder('fl')
            ->orderBy('fl.group', 'ASC')
            ->addOrderBy('fl.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $results;
    }

    /**
     * @param FooterLinkGroup $group
     *
     * @return FooterLink[]
    */
    public function findByGroup(FooterLinkGroup $group): array
    {
        /** @var FooterLink[] $results */
        $results = $this->createQueryBuilder('fl')
            ->where('fl.group = :group')
            ->setParameter('group', $group)
            ->orderBy('fl.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $results;
    }
}
