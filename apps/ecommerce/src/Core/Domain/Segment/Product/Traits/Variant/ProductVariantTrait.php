<?php

declare(strict_types=1);

namespace App\Core\Domain\Segment\Product\Traits\Variant;

use App\Core\Domain\Segment\Product\Entity\Variant\ProductVariant;

trait ProductVariantTrait
{
    public function getVariant(): ProductVariant
    {
        return $this->variant;
    }

    public function setVariant(ProductVariant $variant): self
    {
        $this->variant = $variant;
        return $this;
    }
}
