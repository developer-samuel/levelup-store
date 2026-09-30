<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

use Doctrine\ORM\QueryBuilder;

trait IterableQuery
{
    /**
     * @param QueryBuilder $qb
     *
     * @return iterable<mixed>
    */
    private function getIterableResult(QueryBuilder $qb): iterable
    {
        return $qb->getQuery()
            ->toIterable();
    }
}
