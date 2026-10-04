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

/** @extends AbstractRepository<FooterLink> */
final class FooterLinkRepository extends AbstractRepository implements FooterLinkRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            FooterLink::class,
        );
    }

    protected function getAlias(): string
    {
        return 'fl';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'position';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /** @return FooterLink[] */
    public function findAllOrderedByGroup(): array
    {
        /** @var FooterLink[] $results */
        $results = $this->createQueryBuilder('fl')
            ->orderBy('fl.group', SortDirection::ASC->sort())
            ->addOrderBy('fl.position', SortDirection::ASC->sort())
            ->getQuery()
            ->getResult();

        return $results;
    }

    /** @return FooterLink[] */
    public function findByGroup(FooterLinkGroup $group): array
    {
        /** @var FooterLink[] $results */
        $results = $this->createQueryBuilder('fl')
            ->where('fl.group = :group')
            ->setParameter('group', $group)
            ->orderBy('fl.position', SortDirection::ASC->sort())
            ->getQuery()
            ->getResult();

        return $results;
    }
}
