<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Synonym;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Synonym\Synonym;
use App\Core\Ports\Segment\Synonym\SynonymRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/** @extends AbstractRepository<Synonym> */
final class SynonymRepository extends AbstractRepository implements SynonymRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Synonym::class,
        );
    }

    protected function getAlias(): string
    {
        return 's';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /** @return list<Synonym> */
    public function findAll(): array
    {
        /** @var list<Synonym> */
        return parent::findAll();
    }
}
