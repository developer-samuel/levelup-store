<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits;

use App\Core\Domain\Segment\Product\Entity\Product;

/**
 * @property Product $product
*/
trait ProductTrait
{
    /**
     * @return Product
    */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return self
    */
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }
}
