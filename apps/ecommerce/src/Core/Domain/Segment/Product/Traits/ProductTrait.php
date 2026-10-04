<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits;

use App\Core\Domain\Segment\Product\Entity\Product;

trait ProductTrait
{
    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }
}
