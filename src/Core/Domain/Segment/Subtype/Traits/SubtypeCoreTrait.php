<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Subtype\Traits;

use Doctrine\Common\Collections\Collection;

use App\Core\Domain\Segment\Product\Entity\ProductSubtype;

/**
 * @property Collection<int, ProductSubtype> $productSubtypes
*/
trait SubtypeCoreTrait
{
    /**
     * @return Collection<int, ProductSubtype>
    */
    public function getProductSubtypes(): Collection
    {
        return $this->productSubtypes;
    }

    /**
     * @return bool
    */
    public function hasVariants(): bool
    {
        /** @var ProductSubtype $productSubtype */
        foreach ($this->productSubtypes as $productSubtype) {
            $product = $productSubtype->getProduct();
            if ($product !== null && !$product->getVariants()->isEmpty()) {
                return true;
            }
        }
        
        return false;
    }
}
