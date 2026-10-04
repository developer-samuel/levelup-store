<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Type;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\{
    Segment\Category\Entity\Category,
    Segment\Type\Entity\Type
};

use App\Core\Ports\Segment\Type\TypeRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection,
    Shared\Traits\SingleResult
};

/** @extends AbstractRepository<Type> */
final class TypeRepository extends AbstractRepository implements TypeRepositoryContract
{
    use SingleResult;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Type::class,
        );
    }

    protected function getAlias(): string
    {
        return 't';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    public function findByName(string $name): ?Type
    {
        return $this->findOneByColumn('name', $name);
    }

    public function findByCategoryAndName(Category $category, string $name): ?Type
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.category = :category')
            ->andWhere('LOWER(t.name) = LOWER(:name)')
            ->setParameter('category', $category)
            ->setParameter('name', str_replace('-', ' ', $name));

        $type = $this->getResultOrNull($qb);

        return $type instanceof Type ? $type : null;
    }
}
