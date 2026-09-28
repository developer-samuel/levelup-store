<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Category\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Category\Entity\Category;

use App\Core\Ports\Segment\Category\Repository\CategoryRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/**
 * @extends AbstractRepository<Category>
*/
final class CategoryRepository extends AbstractRepository implements CategoryRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Category::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'c';
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
     * @return Category|null
    */
    public function findByName(string $name): ?Category
    {
        return $this->findOneByColumn('name', $name);
    }
}
