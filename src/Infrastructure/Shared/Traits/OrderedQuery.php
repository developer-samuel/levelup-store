<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

use Doctrine\ORM\QueryBuilder;

use App\Infrastructure\Shared\Enum\SortDirection;

trait OrderedQuery
{
    use IterableQuery;
    use IterableCollector;

    /**
     * @template T of object
     *
     * @param QueryBuilder $qb
     * @param string $alias
     * @param class-string<T> $entityClass
     * @param string $orderByColumn
     * @param SortDirection $direction
     *
     * @return T[]
    */
    protected function getOrderedResults(
        QueryBuilder $qb,
        string $alias,
        string $entityClass,
        string $orderByColumn = 'id',
        SortDirection $direction = SortDirection::ASC,
    ): array {
        $qb->orderBy(sprintf('%s.%s', $alias, $orderByColumn), $direction->sort());

        return $this->collectFromIterable(
            $this->getIterableResult($qb),
            $entityClass,
        );
    }
}
