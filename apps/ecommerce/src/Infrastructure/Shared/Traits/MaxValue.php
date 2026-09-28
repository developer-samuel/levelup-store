<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Traits;

trait MaxValue
{
    use SingleResult;

    /**
     * @param string $field
     * @param array<string, mixed> $criteria
     *
     * @return int
     */
    private function getMaxValue(string $field, array $criteria = []): int
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->select(sprintf('MAX(%s.%s)', $this->getAlias(), $field));

        foreach ($criteria as $column => $value) {
            $qb->andWhere(sprintf('%s.%s = :%s', $this->getAlias(), $column, $column))
               ->setParameter($column, $value);
        }

        return $this->getScalarIntResult($qb);
    }
}
