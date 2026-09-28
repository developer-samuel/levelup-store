<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\Segment\Product\Entity\Product;

/**
 * @property Collection<int, Product> $products
*/
trait ProductCollectionTrait
{
    /**
     * @return Collection<int, Product>
    */
    public function getProducts(): Collection
    {
        return $this->products;
    }
}
