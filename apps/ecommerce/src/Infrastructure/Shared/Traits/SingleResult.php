<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

use Doctrine\ORM\QueryBuilder;

trait SingleResult
{
    /**
     * @param QueryBuilder $qb
     *
     * @return mixed
    */
    private function getResultOrNull(QueryBuilder $qb): mixed
    {
        return $qb->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return int
    */
    private function getScalarIntResult(QueryBuilder $qb): int
    {
        return (int) $qb->getQuery()
            ->getSingleScalarResult();
    }
}
