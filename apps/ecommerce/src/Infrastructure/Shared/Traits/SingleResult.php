<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

use Doctrine\ORM\QueryBuilder;

trait SingleResult
{
    private function getResultOrNull(QueryBuilder $qb): mixed
    {
        return $qb->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getScalarIntResult(QueryBuilder $qb): int
    {
        return (int) $qb->getQuery()
            ->getSingleScalarResult();
    }
}
