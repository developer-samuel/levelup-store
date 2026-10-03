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

/** @extends AbstractRepository<Product> */
final class ProductRepository extends AbstractRepository implements ProductRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            Product::class,
        );
    }

    protected function getAlias(): string
    {
        return 'p';
    }

    protected function getFindAllSortColumn(): string
    {
        return 'id';
    }

    protected function getFindAllSortDirection(): SortDirection
    {
        return SortDirection::ASC;
    }

    public function findById(int $id): ?Product
    {
        return $this->find($id);
    }
}
