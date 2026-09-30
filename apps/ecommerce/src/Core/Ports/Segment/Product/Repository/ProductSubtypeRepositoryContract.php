<?php

declare(strict_types=1);

namespace App\Core\Ports\Segment\Product\Repository;

use App\Core\Domain\Segment\Product\Entity\ProductSubtype;

interface ProductSubtypeRepositoryContract
{
    /**
     * @param int $productId
     *
     * @return ProductSubtype[]
    */
    public function findAllByProductId(int $productId): array;
}
