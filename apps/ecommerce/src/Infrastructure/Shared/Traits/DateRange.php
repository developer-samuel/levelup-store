<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

use Doctrine\ORM\QueryBuilder;

trait DateRange
{
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     *
     * @return QueryBuilder
     */
    private function applyDateRange(
        QueryBuilder $qb,
        string $alias,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
    ): QueryBuilder {
        return $qb
            ->andWhere(sprintf('%s.%s >= :from', $alias, 'createdAt'))
            ->andWhere(sprintf('%s.%s <= :to', $alias, 'createdAt'))
            ->setParameter('from', $from)
            ->setParameter('to', $to);
    }
}
