<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository;

use App\Core\Domain\Segment\Product\Entity\Product;

interface ProductRepositoryContract
{
    /**
     * @return Product[]
    */
    public function findAll(): array;

    /**
     * @param int $id
     *
     * @return Product|null
    */
    public function findById(int $id): ?Product;
}
