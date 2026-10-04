<?php

declare(strict_types=1);

namespace App\Infrastructure\Abstract\Repository;

use Doctrine\{
    Bundle\DoctrineBundle\Repository\ServiceEntityRepository,
    ORM\QueryBuilder,
    Persistence\ManagerRegistry
};

use App\Infrastructure\{
    Shared\Enum\SortDirection,
    Shared\Traits\IterableCollector,
    Shared\Traits\IterableQuery,
    Shared\Traits\SingleResult
};

/**
 * @template TEntity of object
 *
 * @extends ServiceEntityRepository<TEntity>
*/
abstract class AbstractRepository extends ServiceEntityRepository
{
    use SingleResult;
    use IterableQuery;
    use IterableCollector;

    /** @param class-string<TEntity> $entityClass */
    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
    ) {
        parent::__construct(
            $registry,
            $entityClass,
        );
    }

    abstract protected function getAlias(): string;
    abstract protected function getFindAllSortColumn(): string;
    abstract protected function getFindAllSortDirection(): SortDirection;

    protected function createBaseQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder($this->getAlias())
            ->orderBy(
                sprintf('%s.%s', $this->getAlias(), $this->getFindAllSortColumn()),
                $this->getFindAllSortDirection()->sort(),
            );
    }

    protected function existsByField(string $field, string $value): bool
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->select('1')
            ->andWhere(sprintf('%s.%s = :value', $this->getAlias(), $field))
            ->setParameter('value', $value);

        return $this->getResultOrNull($qb) !== null;
    }

    /**
     * @template T of object
     *
     * @param iterable<mixed> $items
     * @param class-string<T> $className
     *
     * @return T[]
    */
    protected function iteratorCollection(iterable $items, string $className): array
    {
        return $this->collectFromIterable($items, $className);
    }

    /** @return list<TEntity> */
    public function findAll(): array
    {
        $qb = $this->createBaseQueryBuilder();

        return $this->collectFromIterable(
            $this->getIterableResult($qb),
            $this->getEntityName(),
        );
    }

    /** @return TEntity|null */
    public function findOneByColumn(string $column, string $value): ?object
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->andWhere('LOWER(' . $this->getAlias() . '.' . $column . ') = LOWER(:value)')
            ->setParameter('value', $value);

        /** @var TEntity|null $result */
        $result = $this->getResultOrNull($qb);

        return $result;
    }
}
