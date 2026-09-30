<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

/**
 * @property ProductVariant $variant
*/
trait ProductVariantTrait
{
    /**
     * @return ProductVariant
    */
    public function getVariant(): ProductVariant
    {
        return $this->variant;
    }

    /**
     * @param ProductVariant $variant
     *
     * @return self
    */
    public function setVariant(ProductVariant $variant): self
    {
        $this->variant = $variant;
        return $this;
    }
}
