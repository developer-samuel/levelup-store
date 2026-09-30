<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Product\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Product\Entity\Product;

use App\Core\Ports\Segment\Product\Repository\ProductRepositoryContract;

use App\Infrastructure\{
    Abstract\Repository\AbstractRepository,
    Shared\Enum\SortDirection
};

/**
 * @extends AbstractRepository<Product>
*/
final class ProductRepository extends AbstractRepository implements ProductRepositoryContract
{
    /**
     * @param ManagerRegistry $registry
    */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Product::class,
        );
    }

    /**
     * @return string
    */
    protected function getAlias(): string
    {
        return 'p';
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
     * @param int $id
     *
     * @return Product|null
    */
    public function findById(int $id): ?Product
    {
        return $this->find($id);
    }
}
