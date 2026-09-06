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

/**
 * @extends AbstractRepository<Type>
*/
final class TypeRepository extends AbstractRepository implements TypeRepositoryContract
{
    use SingleResult;

    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Type::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 't';
    }

    /**
     * @return string
    */
    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    /**
     * @return SortDirection
    */
    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    /**
     * @param string $name
     *
     * @return Type|null
    */
    public function findByName(string $name): ?Type
    {
        return $this->findOneByColumn('name', $name);
    }

    /**
     * @param Category $category
     * @param string $name
     *
     * @return Type|null
    */
    public function findByCategoryAndName(Category $category, string $name): ?Type
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.category = :category')
            ->andWhere('LOWER(t.name) = LOWER(:name)')
            ->setParameter('category', $category)
            ->setParameter('name', ucfirst($name));

        $type = $this->getResultOrNull($qb);

        return $type instanceof Type ? $type : null;
    }
}
